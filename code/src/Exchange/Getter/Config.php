<?php

namespace Bot\Exchange\Getter;

/**
 * Class Config
 *
 * @package \Bot\Exchange\Getter
 */
class Config {

    public static string $iniName = "config.ini";

    /*
     * Return full ini config
     */

    public static function getConfig(): array|bool {
        return parse_ini_file(self::$iniName, true);
    }

    /*
     * Return DB config
     */

    public static function getDb(): array|bool {
        return self::getSection('db');
    }

    /*
     * Return Redis DB config
     */

    public static function getRedis(): array|bool {
        return self::getSection('redis');
    }

    /*
     * Return sleep timeout
     */

    public static function getTimeout(): int|bool {
        $arr = self::getSection('main');
        return isset($arr['timeout']) ? $arr['timeout'] : false;
    }

    /*
     * Return request count before process restarted for memleak protection
     */

    public static function getAllowedRequestCount(): int|bool {
        $arr = self::getSection('main');
        return isset($arr['request_count']) ? $arr['request_count'] : false;
    }

    /*
     * Return DB API endpoint config
     */

    public static function getApis(): array|bool {
        return self::getSection('apis');
    }

    /*
     * Return config by section name
     */

    public static function getSection(string $name): array|bool {
        $iniArray = self::getConfig();
        return $iniArray[$name] ? $iniArray[$name] : false;
    }

}
