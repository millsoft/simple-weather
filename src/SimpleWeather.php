<?php
declare(strict_types=1);

namespace Millsoft\SimpleWeather;

final class SimpleWeather
{

    /**
     * @param string $city
     *
     * @return Forecast
     */
    public function getForecast($city)
    {

        $cache = new Cache();
        $cachedEntry = $cache->getCache($city);
        if ($cachedEntry !== null) {
            return unserialize($cachedEntry);
        }

        $coordinates = $this->getCoordinates($city);
        $forecast = $this->httpRequest("https://api.open-meteo.com/v1/forecast?latitude=${coordinates['latitude']}&longitude=${coordinates['longitude']}&current=temperature_2m,precipitation,rain,showers,snowfall");

        $pForecast = new Forecast();
        $pForecast->temperature = $forecast['current']['temperature_2m'] . ' ' . $forecast['current_units']['temperature_2m'];

        //set cache:
        $cache->setCache($city, serialize($pForecast));

        return $pForecast;

    }

    private function getCoordinates($city)
    {
        $coordinates = $this->httpRequest("https://geocoding-api.open-meteo.com/v1/search?name=$city&count=1&language=en&format=json");
        $cityCoordinates = $coordinates['results'][0];

        return [
            'latitude' => $cityCoordinates['latitude'],
            'longitude' => $cityCoordinates['longitude'],
        ];

    }

    private function httpRequest($url)
    {
        $data = wp_remote_get($url);
        return json_decode($data['body'], true);
    }
}
