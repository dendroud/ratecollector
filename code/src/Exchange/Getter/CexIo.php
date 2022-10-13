<?php

namespace Bot\Exchange\Getter;

/**
 * Description of CexIo
 *
 * @author user1
 */
class CexIo extends RateExchange implements RateExchangeService {

    function __construct(array $config = []) {
        
    }

    public function getRate(string $currName1, string $currName2): float {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://cex.io/api/last_price/{$currName1}/{$currName2}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
        return 0;
    }

}
