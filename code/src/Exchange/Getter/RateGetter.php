<?php

namespace Bot\Exchange\Getter;

/**
 * Base class for request rates
 *
 * @package \Bot\Exchange\Getter
 */
class RateGetter{

    /**
     * @var RateExchangeService object of class based on RateExchangeService
     */
    private RateExchangeService $exchangerService;

    /**
     * Constructor for dependency injection
     * @param RateExchangeService $exchangerService object of class based on RateExchangeService
     */
    function __construct(RateExchangeService $exchangerService) {
        $this->exchangerService = $exchangerService;
    }
    
    /**
     * Function for dependency injection call
     * @param string $currName1
     * @param string $currName2
     * @return float|null
     */
    public function getRate(string $currName1, string $currName2): ?float{
        return $this->exchangerService->getRate($currName1, $currName2);
    }

}
