<?php

namespace Bot\Exchange\Getter;

/**
 * Interface RateExchange
 * @package \Bot\Exchange\Getter
 */
interface RateExchangeService {

    const CURR_ID = 'id';
    const CURR_SYMBOL = 'symbol';
    const CURR_NAME = 'name';

    /**
     * Rate request function
     * @param string $currName1
     * @param string $currName2
     * @return float
     * @throws Exception
     */
    public function getRate(string $currName1, string $currName2): ?float;
}
