<?php

declare(strict_types=1);

namespace Millsoft\SimpleWeather;

final class Cache
{

    private const CACHE_VALID_TIME = 300;  //in seconds

    public static function getTableName()
    {
        global $wpdb;
        return $wpdb->prefix . 'millsoft_simpleweather_cache';
    }

    public function getCache($key)
    {
        global $wpdb;
        $tableName = self::getTableName();
        $prepared = $wpdb->prepare("SELECT * FROM $tableName WHERE updated_at > DATE_SUB(now(), INTERVAL " . self::CACHE_VALID_TIME . " SECOND) AND cache_key = %s", [
            $key
        ]);
        $cachedEntry = $wpdb->get_row($prepared);

        if ($cachedEntry === null) {
            return null;
        }

        return $cachedEntry->cache_value;

    }

    public function setCache($key, $value)
    {
        global $wpdb;
        $tableName = self::getTableName();
        $prepared = $wpdb->prepare("INSERT INTO $tableName SET cache_key = %s, updated_at = NOW(), cache_value = %s ON DUPLICATE KEY UPDATE cache_value = %s, updated_at = NOW()", [
            $key,
            $value,
            $value,
        ]);

        $re = $wpdb->query($prepared);

    }

}
