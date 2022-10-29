<?php

namespace Bot\Exchange\Getter;

/**
 * Kraken class for receiving rates
 * API docs https://docs.kraken.com/rest/#tag/Market-Data/operation/getTickerInformation
 */
class Kraken extends RateExchange implements RateExchangeService {

    /**
     * Class constructor
     * @param array $config config array for API endpoint from config file. Left for compatibility
     */
    function __construct(array $config = []) {
        
    }

    
    /**
     * {@inheritdoc }
     * @param string $currName1
     * @param string $currName2
     * @return float|null
     * @throws \Exception if can't receive rate
     */
    public function getRate(string $currName1, string $currName2): ?float {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.kraken.com/0/public/Ticker?pair={$currName1}{$currName2}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 4,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $rate = null;

        $resp = json_decode($response, true);
        if ($resp) {
            if (empty($resp['error']) && !empty($resp['result'])) {
                $keys = array_keys($resp['result']);
                if (isset($resp['result'][$keys[0]]['c'][0])) {
                    //key 'c' is a Last trade closed. Look documentation
                    $rate = $resp['result'][$keys[0]]['c'][0];
                } else {
                    throw new \Exception("Rate not received. Currencies: {$currName1}, {$currName2}. Error: no value in resp array");
                }
            } else {
                throw new \Exception("Rate not received. Currencies: {$currName1}, {$currName2}. Error: " . join(', ', $resp['error']));
            }
        } else {
            throw new \Exception("Rate not received. Currencies: {$currName1}, {$currName2}");
        }

        return $rate;
    }

}
