<?php

declare(strict_types=1);

namespace Millsoft\SimpleWeather;

final class Installer
{
    public static function install()
    {
        global $wpdb;

        $table_name = Cache::getTableName();
        $charset_collate = $wpdb->get_charset_collate();

        $sql = <<<SQL
            CREATE TABLE {$table_name} (
                id INT AUTO_INCREMENT NOT NULL,
                cache_key VARCHAR(255) NULL,
                updated_at DATETIME DEFAULT NULL,
                cache_value TEXT NULL,
                UNIQUE KEY millsoft_sw_cache_unique_key (cache_key),
                PRIMARY KEY (id)
            ) $charset_collate;
SQL;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public static function uninstall()
    {
        global $wpdb;
        $table_name = Cache::getTableName();
        $wpdb->query("DROP TABLE {$table_name}");
    }

}
