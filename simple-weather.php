<?php
/**
 * Simple Weather Plugin for WordPress
 *
 * @package   millsoft/simple-weather
 * @link      https://github.com/millsoft/simple-weather
 * @author    Michael Milawski
 * @copyright 2024 Michael Milawski
 * @license   GPL v2 or later
 *
 * Plugin Name:  Simple Weather
 * Description:  Shows simple weather information using a shortcode
 * Version:      0.0.1
 * Plugin URI:   https://www.millsoft.de
 * Author:       Michael Milawski
 * Author URI:   https://www.millsoft.de
 * Text Domain:  simple-weather
 * Domain Path:  /languages/
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * License URI:  https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * License:      GPL v2 or later
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */


use Millsoft\SimpleWeather\Installer;

$autoloader = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoloader)) {
    die("[ERROR] Autoloader not found. Execute composer install");
}

require_once $autoloader;

/**
 * The [simple-weather] shortcode.
 *
 * Accepts a title and will display a box.
 *
 * @param array  $atts    Shortcode attributes. Default empty.
 * @param string $content Location. Default null.
 * @param string $tag     Shortcode tag (name). Default empty.
 *
 * @return string Shortcode output.
 */
function millsoft_simpleweather_shortcode($atts = [], $content = null, $tag = '')
{

    // normalize attribute keys, lowercase
    $atts = array_change_key_case((array)$atts, CASE_LOWER);

    // override default attributes with user attributes
    $sw_atts = shortcode_atts(
        array(
            'location' => 'Berlin',
        ), $atts, $tag
    );

    $o = '<div class="simple-weather">';

    $simpleWeather = new Millsoft\SimpleWeather\SimpleWeather();
    $forecast = $simpleWeather->getForecast($sw_atts['location']);

    $o .= esc_html($sw_atts['location']) . ': ' . $forecast->temperature;

    if (!is_null($content)) {
        $o .= apply_filters('the_content', $content);
    }

    $o .= '</div>';

    // return output
    return $o;
}

function millsoft_simpleweather_shortcode_init()
{
    add_shortcode('simple-weather', 'millsoft_simpleweather_shortcode');
}

add_action('init', 'millsoft_simpleweather_shortcode_init');

$installer = new Installer();
register_activation_hook(__FILE__, [$installer, 'install']);
register_deactivation_hook(__FILE__, [$installer, 'uninstall']);
