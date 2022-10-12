<?php

/**
 * File with infinity loop, which restart working process for memleak protection
 *
 */

//TODO: add pcntl support for cntrl+c handling

while (true) 
{
    flush();
    $fp = popen("/usr/local/bin/php -f rates.php", "r");
    while (!feof($fp)) {
        // send the current file part to the terminal
        print fread($fp, 1024);
        // flush the content to the terminal
        flush();
    }
    fclose($fp);
}