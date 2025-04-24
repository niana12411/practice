<?php

namespace App\Services;


use App\Exceptions\OpenweathermapException;
use App\Api\OpenWeatherGateway;

class WeatherService
{
    protected $api;
    public function __construct(OpenWeatherGateway $OpenWeatherGateway)
    {
        $this->api = $OpenWeatherGateway;
    }

    // 查詢天氣資訊
    public function getWeatherApi($request)
    {

        $data = $this->api->getCityWeatherApi($request['country'],$request['city']);
       
        if(!isset($data['weather'][0]['description']) || 
        !isset($data['main']['temp_min']) ||  
        !isset($data['main']['temp_max'])){
            throw new OpenweathermapException(OpenweathermapException::RESPONSE_CODE_ERROR);
        }

        return [
            'weather_description' => $data['weather'][0]['description'],
            'temp_min' => $data['main']['temp_min'],
            'temp_max' => $data['main']['temp_max'],
        ];
    }
}
