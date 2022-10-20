<?php
/**
 * Script with loop for starting all processes with timeout
 */
require 'vendor/autoload.php';

use Bot\Exchange\Getter\RateProcessor;
use Bot\Exchange\Getter\Config;

$rateProcessor = new RateProcessor();
$timeout = Config::getTimeout();

echo "Started new process" . PHP_EOL;

//repeat_count from main section in config file
$repCount = Config::getAllowedRepeatCount();

for($i = 0; $i < $repCount; $i++) {
    $rateProcessor->processProviders();
    echo "Sleeping {$timeout} sec" . PHP_EOL;
    sleep($timeout);
}
