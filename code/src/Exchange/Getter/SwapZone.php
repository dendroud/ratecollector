<?php

namespace Bot\Exchange\Getter;

/**
 * Description of SwapZone
 *
 * @author user1
 */
class SwapZone extends RateExchange implements RateExchangeService {

    function __construct(array $config = []) {
        $this->currencies = [
            'BTC' => [self::CURR_ID => 'bitcoin', self::CURR_NAME => 'Bitcoin', self::CURR_SYMBOL => 'btc'],
            'ETH' => [self::CURR_ID => 'ethereum', self::CURR_NAME => 'Ethereum', self::CURR_SYMBOL => 'eth'],
            'XMR' => [self::CURR_ID => 'monero', self::CURR_NAME => 'Monero', self::CURR_SYMBOL => 'xmr'],
            'USD' => [self::CURR_ID => 'uniswap-state-dollar', self::CURR_NAME => 'unified Stable Dollar', self::CURR_SYMBOL => 'usd']];
        $this->config = $config;
    }

    public function getRate(string $currName1, string $currName2): float {
        $curr1 = $this->getProviderCurrency($currName1);
        $curr2 = $this->getProviderCurrency($currName2);
        

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.swapzone.io/v1/exchange/get-rate?from={$curr2[self::CURR_SYMBOL]}&to={$curr1[self::CURR_SYMBOL]}&amount=1000&rateType=all&availableInUSA=false&chooseRate=best&noRefundAddress=false",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'x-api-key: ' . $this->config['key']
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }

}
