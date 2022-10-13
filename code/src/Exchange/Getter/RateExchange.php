<?php

namespace Bot\Exchange\Getter;

/**
 * Description of RateExchange
 *
 * @author user1
 */
abstract class RateExchange {

    protected array $currencies;
    protected $client;
    protected $config;

    public function getProviderCurrency(string $currName): array {
        if (isset($this->currencies[$currName])) {
           return $this->currencies[$currName];
        } else {
            throw new \Exception("Undefined currency: {$currName}");
        }
    }

}
