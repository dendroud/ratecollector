<?php

namespace Bot\Exchange\Getter;

use Codenixsv\CoinGeckoApi\CoinGeckoClient;

/**
 * CoinGecko rate exchange class
 * API docs https://www.coingecko.com/en/api/documentation
 */
class CoinGecko extends RateExchange implements RateExchangeService {

    /**
     * Class constructor
     * @param array $config config array for API endpoint from config file
     */
    function __construct(array $config = []) {
        //build currency info array for this provider
        $this->currencies = [
            'BTC' => [self::CURR_ID => 'bitcoin', self::CURR_NAME => 'Bitcoin', self::CURR_SYMBOL => 'btc'],
            'ETH' => [self::CURR_ID => 'ethereum', self::CURR_NAME => 'Ethereum', self::CURR_SYMBOL => 'eth'],
            'XMR' => [self::CURR_ID => 'monero', self::CURR_NAME => 'Monero', self::CURR_SYMBOL => 'xmr'],
            'USD' => [self::CURR_ID => 'uniswap-state-dollar', self::CURR_NAME => 'unified Stable Dollar', self::CURR_SYMBOL => 'usd']];
        $this->client = new CoinGeckoClient();
    }

    /**
     * {@inheritdoc }
     * @param string $currName1
     * @param string $currName2
     * @return float|null
     * @throws \Exception if can't receive rate
     */
    public function getRate(string $currName1, string $currName2): ?float {
        $curr1 = $this->getProviderCurrency($currName1);
        $curr2 = $this->getProviderCurrency($currName2);

        $data = $this->client->simple()->getPrice($curr1[self::CURR_ID], $curr2[self::CURR_SYMBOL]);

        if (!isset($data[$curr1[self::CURR_ID]][$curr2[self::CURR_SYMBOL]])) {
            throw new \Exception("Rate not received. Currencies: {$currName1}, {$currName2}");
        }
        return $data[$curr1[self::CURR_ID]][$curr2[self::CURR_SYMBOL]];
    }

}
