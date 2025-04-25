<?php

namespace App\Services;


use App\Exceptions\OpenweathermapException;
use App\Api\OpenWeatherGateway;
use Illuminate\Support\Facades\Cache;

class WeatherService
{
    protected $api;
    public function __construct(OpenWeatherGateway $OpenWeatherGateway)
    {
        $this->api = $OpenWeatherGateway;
    }


    // 查詢天氣資訊
    public function getWeatherData($request)
    {

        $data = $request->only(['country', 'city']);

        //檢查快取
        $cacheKey = "weather:{$data['country']}:{$data['city']}";
        $cachedData = Cache::get($cacheKey);

        if ($cachedData) {
            return $cachedData;
        }

        //查詢天氣資料 API
        $apiData = $this->api->getCityWeatherApi($data['country'],$data['city']);
       
        if(!isset($apiData['weather'][0]['description']) || 
        !isset($apiData['main']['temp_min']) ||  
        !isset($apiData['main']['temp_max'])){
            throw new OpenweathermapException(OpenweathermapException::RESPONSE_CODE_ERROR);
        }

        $weatherData = [
            'weather_description' => $apiData['weather'][0]['description'],
            'temp_min' => $apiData['main']['temp_min'],
            'temp_max' => $apiData['main']['temp_max'],
        ];

        //寫入快取
        Cache::put($cacheKey, $weatherData, 3600);
        return $weatherData;
    }
}
