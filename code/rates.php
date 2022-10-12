<?php

require 'vendor/autoload.php';

use Bot\Exchange\Getter\RateProcessor;
use Bot\Exchange\Getter\Config;

$rateProcessor = new RateProcessor();
$timeout = Config::getTimeout();

echo "Started new process" . PHP_EOL;

$recCount = Config::getAllowedRequestCount();

for($i = 0; $i < $recCount; $i++) {
    $rateProcessor->processRates();
    echo "Sleeping {$timeout} sec" . PHP_EOL;
    sleep($timeout);
}
die();