<?php

namespace App\Services;


use App\Exceptions;
use Ixudra\Curl\Facades\Curl;
use App\Exceptions\OpenweathermapException;

class WeatherService
{
    // 查詢天氣資訊
    public function getWeatherApi($request)
    {
        $request = http_build_query([
            'units' => 'metric',
            'q' => $request['city'].','.$request['country'],
            'appid' => config('weather.openweathermap.appid'),
            'lang' => config('weather.openweathermap.lang'),
        ]);

        $res_curl = Curl::to(config('weather.openweathermap.api_url').'?'.$request)
        ->withHeader('Content-Type: application/json')
        ->withHeader('Accept: application/json')
        ->returnResponseArray()
        ->asJsonResponse(true)
        ->get();

        //TODO:紀錄CURL API Log
        // dump($res_curl);

        if(!isset($res_curl)){
            throw new OpenweathermapException(OpenweathermapException::CURL_REQUEST_ERROR);
        }
        if($res_curl['status'] == 404){
            throw new OpenweathermapException(OpenweathermapException::CITY_ID_NOT_EXIST);
        }
        if($res_curl['status'] != 200){
            throw new OpenweathermapException(OpenweathermapException::HTTP_CODE_ERROR);
        }
        if(!isset($res_curl['content'])){
            throw new OpenweathermapException(OpenweathermapException::RESPONSE_CODE_ERROR);
        }

        $data = $res_curl['content'];
       
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
