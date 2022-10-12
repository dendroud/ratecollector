<?php

namespace Bot\Exchange\Getter;

/**
 * Base class for request rates
 *
 * @package \Bot\Exchange\Getter
 */
class RateGetter{

    private $exchangerService;

    function __construct(RateExchangeService $exchangerService) {
        $this->exchangerService = $exchangerService;
    }
    
    public function getRate(string $currName1, string $currName2): float{
        return $this->exchangerService->getRate($currName1, $currName2);
    }

}
