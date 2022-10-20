<?php

namespace Bot\Exchange\Getter;

/**
 * Abstract RateExchange class
 * @abstract
 */
abstract class RateExchange {

    /**
     * Currencies configuration for concret implementation 
     * @var array
     */
    protected array $currencies;
    
    /**
     * API endpoint object.
     * Variable for client object from client library
     */
    protected $client;
    
    /**
     * Config for API endpoint object 
     * @var array
     */
    protected array $config;

    /**
     * Return element from $this->currencies array by name for unification 
     * @param string $currName
     * @return array
     * @throws \Exception if currency name not found in $this->currencies array
     */
    public function getProviderCurrency(string $currName): array {
        if (isset($this->currencies[$currName])) {
            return $this->currencies[$currName];
        } else {
            throw new \Exception("Undefined currency: {$currName}");
        }
    }

}
