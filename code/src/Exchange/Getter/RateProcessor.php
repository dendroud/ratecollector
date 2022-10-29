<?php

namespace Bot\Exchange\Getter;

use Bot\Exchange\Getter\Config;
use Bot\Exchange\Getter\RateGetter;
use Redis;

/**
 * RateProcessor class
 * Make initiation of rate request, save and cache received rates
 *
 */
class RateProcessor {

    /**
     * PgSql\Connection instance on success, or false on failure
     * @internal
     * @var PgSql\Connection|false 
     */
    protected $dbConnection = null;

    /**
     * DB config array
     * @internal
     * @var array 
     */
    protected $dbConfig = null;

    /**
     * PgSql\Connection instance on success, or false on failure
     * @internal
     * @var PgSql\Connection|false 
     */
    protected $redis = null;

    function __construct() {
        $this->dbConfig = Config::getDb();
        $this->redis = new Redis();
    }

    /**
     * Connect to cache service 
     */
    protected function cacheConnect() {
        $redisConf = Config::getRedis();
        $this->redis->connect($redisConf['server'], $redisConf['port']);
    }

    /**
     * Connect to database 
     * @return PgSql\Connection|false 
     */
    protected function dbConnect() {
        $this->dbConnection = pg_connect("host={$this->dbConfig['server']} dbname={$this->dbConfig['name']} user={$this->dbConfig['user']} password={$this->dbConfig['password']}");
        return $this->dbConnection;
    }

    /**
     * Save rate into cache
     * @param string $key
     * @param float $value
     */
    protected function cacheRate(string $key, float $value) {
        if ($this->redis->ping()) {
            try {
                $this->redis->set($key, $value);
            } catch (RedisException $ex) {
                echo 'Redis set exception:' . PHP_EOL;
                echo $ex->getTraceAsString() . PHP_EOL;
                echo $ex->getMessage() . PHP_EOL;
            }
        } else {
            echo "Redis is not connected" . PHP_EOL;
        }
    }

    /**
     * Combine cache key by input parameters
     * @param string $provider
     * @param string $curr1
     * @param string $curr2
     * @return string cache key
     */
    public static function calcRateCacheKey(string $provider, string $curr1, string $curr2): string {
        return "{$provider}:{$curr1}:{$curr2}";
    }

    /**
     * Loop for processing API endpoints
     */
    public function processProviders() {
        $endpoints = Config::getApis();

        $this->cacheConnect();

        foreach ($endpoints as $provider => $api) {
            $this->processProvider($provider, $api);
        }

        if ($this->redis) {
            $this->redis->close();
        }
    }

    /**
     * Loop by currency pairs for API endpoint
     * @param string $provider
     * @param array $api
     * @return bool  true if rates saved into DB othervice false
     */
    public function processProvider(string $provider, array $api): bool {
        $sqlRateBulkResults = [];
        $pairs = str_getcsv($api['pairs']);
        foreach ($pairs as $pair) {
            $currencies = explode('/', $pair);
            $rate = $this->processRate($provider, $api, $currencies);

            if ($rate) {
                $cacheKey = self::calcRateCacheKey($provider, $currencies[0], $currencies[1]);
                $this->cacheRate($cacheKey, $rate);
                $sqlRateBulkResults[] = "('{$provider}', '{$currencies[0]}', '{$currencies[1]}','{$rate}')";
            }
        }

        //open DB connection
        if (!$this->dbConnect()) {
            echo 'DB connection error. DB config:' . PHP_EOL;
            print_r($this->dbConfig);
            return false;
        }

        $saveRes = true;
        //if rates present then save it into DB
        if (count($sqlRateBulkResults)) {
            $saveRes = $this->saveRates(join(',', $sqlRateBulkResults));
        }

        pg_close($this->dbConnection);

        return $saveRes;
    }

    /**
     * Make request for concrete API endpoint by dependency injection by class name from config file
     * @param string $apiName config endpoint name
     * @param array $config config array for endpoint
     * @param array $pair pair of rates like ['BTC', 'USD']
     * @return float|bool rate value or false
     */
    public function processRate(string $apiName, array $config, array $pair): float|bool {
        echo "Request rate for {$pair[0]}, {$pair[1]} in {$config['class_name']}" . PHP_EOL;

        try {
            $className = $config['class_name'];
            $rateService = new $className($config);
            $rateGetter = new RateGetter($rateService);
            $rate = $rateGetter->getRate($pair[0], $pair[1]);
            echo "Rate: {$rate}" . PHP_EOL;
            return $rate;
        } catch (\Exception $exc) {
            echo 'Rate process exception:' . PHP_EOL;
            echo $exc->getTraceAsString() . PHP_EOL;
            echo $exc->getMessage() . PHP_EOL;
        }
        return false;
    }

    /**
     * Save rates into DB
     * @param string $sqlValues SQL prepared values for bulk insert
     * @return bool
     */
    public function saveRates(string $sqlValues): bool {
        $sql = "INSERT INTO rate (provider, cur1, cur2, value) VALUES {$sqlValues}";
        $res = pg_query($this->dbConnection, $sql);
        if (!$res) {
            echo "Can't execute query: {$sql}" . PHP_EOL;
            return false;
        }
        echo "Saved into DB" . PHP_EOL;
        return true;
    }

}
