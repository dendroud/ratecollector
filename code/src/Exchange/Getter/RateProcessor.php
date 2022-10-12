<?php

namespace Bot\Exchange\Getter;

use Bot\Exchange\Getter\Config;
use Bot\Exchange\Getter\RateGetter;
use Redis;

/**
 * Description of RateProcessor
 *
 * @author user1
 */
class RateProcessor {

    protected $dbConnection;
    protected $dbConfig;
    protected $redis;

    function __construct() {
        $this->dbConfig = Config::getDb();
        $this->redis = new Redis();
    }

    protected function cacheConnect() {
        $redisConf = Config::getRedis();
        $this->redis->connect($redisConf['server'], $redisConf['port']);
    }

    protected function dbConnect() {
        $this->dbConnection = pg_connect("host={$this->dbConfig['server']} dbname={$this->dbConfig['name']} user={$this->dbConfig['user']} password={$this->dbConfig['password']}");
        return $this->dbConnection;
    }

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

    public static function calcRateCacheKey(string $provider, string $curr1, string $curr2): string {
        return "{$provider}:{$curr1}:{$curr2}";
    }

    public function processRates(): bool {
        $endpoints = Config::getApis();
        
        $this->cacheConnect();
        
        $sqlRateBulkResults = [];
        foreach ($endpoints as $apiName => $api) {
            $pairs = str_getcsv($api['pairs']);
            foreach ($pairs as $pair) {
                $currencies = explode('/', $pair);
                $rate = $this->processRate($apiName, $api, $currencies);
                if ($rate) {

                    $cacheKey = self::calcRateCacheKey($apiName, $currencies[0], $currencies[1]);
                    $this->cacheRate($cacheKey, $rate);

                    $sqlRateBulkResults[] = "('{$apiName}', '{$currencies[0]}', '{$currencies[1]}','{$rate}')";
                }
            }
        }

        if ($this->redis) {
            $this->redis->close();
        }

        //open DB connection
        if (!$this->dbConnect()) {
            echo 'DB connection error. DB config:' . PHP_EOL;
            print_r($this->dbConfig);
            return false;
        }

        //if rates present then save it into DB
        if (count($sqlRateBulkResults)) {
            $this->saveRates(join(',', $sqlRateBulkResults));
        }

        pg_close($this->dbConnection);

        return true;
    }

    public function processRate(string $apiName, array $config, array $pair): float|bool {
        echo "Request rate for {$pair[0]}, {$pair[1]}" . PHP_EOL;
        try {
            $className = $config['class_name'];
            $rateService = new $className();
            $rateGetter = new RateGetter($rateService);
            $rate = $rateGetter->getRate($pair[0], $pair[1]);
            echo "Rate: {$rate}" . PHP_EOL;
            return $rate;
        } catch (Exception $exc) {
            echo 'Rate process exception:' . PHP_EOL;
            echo $exc->getTraceAsString() . PHP_EOL;
            echo $exc->getMessage() . PHP_EOL;
        }
        return false;
    }

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
