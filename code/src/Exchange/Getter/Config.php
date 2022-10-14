<?php

namespace Bot\Exchange\Getter;

/**
 * Class Config for getting parameters from config file
 *
 * @package \Bot\Exchange\Getter
 */
class Config {

    /**
     * 
     * @var string ini file name
     */
    public static string $iniName = "config.ini";

    /**
     * Return full ini config
     * @return array|bool
     */
    public static function getConfig(): array|bool {
        return parse_ini_file(self::$iniName, true);
    }

    /**
     * Return DB config
     * @return array|bool
     */
    public static function getDb(): array|bool {
        return self::getSection('db');
    }

    /**
     * Return Redis config
     * @return array|bool
     */
    public static function getRedis(): array|bool {
        return self::getSection('redis');
    }

    /**
     * Return sleep timeout or false
     * @return int|bool
     */
    public static function getTimeout(): int|bool {
        $arr = self::getSection('main');
        return isset($arr['timeout']) ? $arr['timeout'] : false;
    }

    /**
     * Return request count before process restarted for memleak protection or false
     * @return int|bool
     */
    public static function getAllowedRepeatCount(): int|bool {
        $arr = self::getSection('main');
        return isset($arr['repeat_count']) ? $arr['repeat_count'] : false;
    }

    /**
     * Return DB API endpoint config or false 
     * @return array|bool
     */
    public static function getApis(): array|bool {
        return self::getSection('apis');
    }

    /**
     * Return config by section name or false
     * @param string $name
     * @return array|bool
     */
    public static function getSection(string $name): array|bool {
        $iniArray = self::getConfig();
        return $iniArray[$name] ? $iniArray[$name] : false;
    }

}
