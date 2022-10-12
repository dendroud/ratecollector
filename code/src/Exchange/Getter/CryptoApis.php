<?php

namespace Bot\Exchange\Getter;

use CryptoAPIs\Configuration;
use CryptoAPIs\Api\AssetsApi;


/**
 * Description of CryptoApis
 *
 * @author user1
 */
class CryptoApis extends RateExchange implements RateExchangeService {
    protected $config;
    function __construct() {
        $this->currencies = [
            'BTC' => [self::CURR_ID => 'bitcoin', self::CURR_NAME => 'Bitcoin', self::CURR_SYMBOL => 'btc'],
            'ETH' => [self::CURR_ID => 'ethereum', self::CURR_NAME => 'Ethereum', self::CURR_SYMBOL => 'eth'],
            'XMR' => [self::CURR_ID => 'monero', self::CURR_NAME => 'Monero', self::CURR_SYMBOL => 'xmr'],
            'USD' => [self::CURR_ID => 'uniswap-state-dollar', self::CURR_NAME => 'unified Stable Dollar', self::CURR_SYMBOL => 'usd']];
        // Configure API key authorization: ApiKey
        
        $this->config = CryptoAPIs\Configuration::getDefaultConfiguration()->setApiKey('x-api-key', 'YOUR_API_KEY');
        $this->client = new CoinGeckoClient();
    }

    public function getRate(string $currName1, string $currName2): float {

// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = CryptoAPIs\Configuration::getDefaultConfiguration()->setApiKeyPrefix('x-api-key', 'Bearer');


        $apiInstance = new CryptoAPIs\Api\ExchangeRatesApi(
                // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
                // This is optional, `GuzzleHttp\Client` will be used as default.
                new GuzzleHttp\Client(),
                $config
        );
        $from_asset_symbol = btc; // string | Defines the base asset symbol to get a rate for.
        $to_asset_symbol = usd; // string | Defines the relation asset symbol in which the base asset rate will be displayed.
        $context = yourExampleString; // string | In batch situations the user can use the context to correlate responses with requests. This property is present regardless of whether the response was successful or returned as an error. `context` is specified by the user.
        $calculation_timestamp = 1635514425; // int | Defines the time of the market data used to calculate the exchange rate in UNIX Timestamp. Oldest possible timestamp is 30 days.

        try {
            $result = $apiInstance->getExchangeRateByAssetSymbols($from_asset_symbol, $to_asset_symbol, $context, $calculation_timestamp);
            print_r($result);
        } catch (Exception $e) {
            echo 'Exception when calling ExchangeRatesApi->getExchangeRateByAssetSymbols: ', $e->getMessage(), PHP_EOL;
        }
    }

}
