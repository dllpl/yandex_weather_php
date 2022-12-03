<?php

/**
 * Класс CityWeather позволяет получить погоду по названию города.
 * Использует: API Яндекс.Геокодер для получения координат по названию города,
 * API Янедекс.Погода для получения погоды в городе.
 * В результате работы возвращет в шаблонизатор index.html необохдимые значения.
 *
 * @author Nikita Iv nick.iv.dev@gmail.com
 */
final class CityWeather
{
    /** @const string */
    const YANDEX_WEATHER_TOKEN = '8e00b9c4-1eab-47c4-b481-f44bbf114495';
    /** @const string */
    const YANDEX_GEOCODER_TOKEN = '837a7aab-3ca6-4d11-bb98-089f871ee337';

    /** @const string */
    const YANDEX_GEOCODER_URL = 'https://geocode-maps.yandex.ru/1.x/?apikey=';
    /** @const string */
    const YANDEX_WEATHER_URL = 'https://api.weather.yandex.ru/v2/informers?';

    /**
     * Получить погоду по названию города или адреса и вернуть эти значения в шаблонизатор
     * @param $city
     * @return void
     */
    public function getWeatherByName($city): void
    {
        $pos = $this->getLocationByName($city);

        $response = $this->request(self::YANDEX_WEATHER_URL . 'lat=' . $pos[1] . '&lon=' . $pos[0],
            [
                'X-Yandex-API-Key: ' . self::YANDEX_WEATHER_TOKEN,
            ]
        );

        $temp_fact = $response['fact']['temp'];
        $temp_feels_like = $response['fact']['feels_like'];
        $link = $response['info']['url'];
        require_once 'index.html';
    }

    /**
     * Получить широту и долготу по названию города или адреса.
     * @param $city
     * @param string $format
     * @param int $results
     * @return array
     */
    private function getLocationByName($city, string $format = 'json', int $results = 1): array
    {
        $response = $this->request(self::YANDEX_GEOCODER_URL . self::YANDEX_GEOCODER_TOKEN . '&format=' . $format . '&geocode=' . urlencode($city) . '&results=' . $results);
        $pos = $response['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos'];
        return explode(' ', $pos);
    }

    /**
     * Метод реквестер
     * @param $url
     * @param array|null $header
     * @return mixed
     */
    private function request($url, null|array $header = null): mixed
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        if ($header) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        } else {
            curl_setopt($curl, CURLOPT_HEADER, false);
        }

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $res = curl_exec($curl);
        curl_close($curl);

        return json_decode($res, true);
    }
}

$new = new CityWeather();
$new->getWeatherByName('Мурманск');

