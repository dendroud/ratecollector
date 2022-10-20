<?php

namespace Bot\Exchange\Getter;

use Bot\Exchange\Getter\Config;
use Bot\Exchange\Getter\RateGetter;
use Bot\Exchange\Getter\CoinGecko;
use Bot\Exchange\Getter\Kraken;

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

    function __construct() {
        $this->dbConfig = Config::getDb();
    }

    /**
     * Return class name by provider config name 
     * @param string $name
     * @return string|bool
     */
    protected function getClassNameForProvider(string $name): string|bool {
        return class_exists(__NAMESPACE__ . '\\' . $name) ? __NAMESPACE__ . '\\' . $name : false;
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
     * Loop for processing API endpoints
     */
    public function processProviders() {
        $endpoints = Config::getApis();

        foreach ($endpoints as $provider => $api) {
            $this->processProvider($provider, $api);
        }
    }

    /**
     * Loop by currency pairs for API endpoint
     * @param string $provider
     * @param array $api
     * @return bool  true if rates saved into DB othervice false
     */
    public function processProvider(string $provider, array $api): bool {
        //open DB connection
        if (!$this->dbConnect()) {
            echo 'DB connection error. DB config:' . PHP_EOL;
            print_r($this->dbConfig);
            return false;
        }

        $pairs = str_getcsv($api['pairs']);
        $saveRes = true;
        foreach ($pairs as $pair) {
            $currencies = explode('/', $pair);
            $rate = $this->processRate($provider, $api, $currencies);
            if ($rate) {
                $rateArr = ['provider' => $provider, 'cur1' => $currencies[0], 'cur2' => $currencies[1], 'rate' => $rate];
                if (!$this->saveRate($rateArr)) {
                    //return false for provider even if not saved one rate
                    $saveRes = false;
                }
            }
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
        echo "Request rate for {$pair[0]}, {$pair[1]} for {$apiName}" . PHP_EOL;

        try {
            $className = $this->getClassNameForProvider($apiName);
            if (!$className) {
                throw new \Exception("Rate not received. Currencies: {$pair[0]}, {$pair[1]}. Error: not found class name {$apiName}");
            }
            $rateService = new $className($config);
            $rate = $rateService->getRate($pair[0], $pair[1]);
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
     * Save rate into DB
     * @param array $rate rate with parameters like ['provider' => $provider, 'cur1' => $currencies[0], 'cur2' => $currencies[1], 'rate' => $rate]
     * @return bool
     */
    public function saveRate(array $rate): bool {
        $sql = "INSERT INTO rate (provider, cur1, cur2, value) VALUES ($1, $2, $3, $4)";
        $res = pg_query_params($this->dbConnection, $sql, [$rate['provider'], $rate['cur1'], $rate['cur2'], $rate['rate']]);
        if (!$res) {
            echo "Can't execute query: {$sql}" . PHP_EOL;
            return false;
        }
        echo "Saved into DB" . PHP_EOL;
        return true;
    }

}
