<?php

namespace App\Api;

use App\Exceptions;
use App\Facade\Common;

use Ixudra\Curl\Facades\Curl;
use App\Exceptions\OpenweathermapException;

use Illuminate\Support\MessageBag;


class OpenWeatherGateway
{
    // 查詢天氣API
    public function getCityWeatherApi(string $country, string $city):array
    {
        $request = http_build_query([
            'units' => 'metric',
            'q' => $city.','.$country,
            'appid' => config('weather.openweathermap.appid'),
            'lang' => config('weather.openweathermap.lang'),
        ]);

        $res_curl = Curl::to(config('weather.openweathermap.api_url').'?'.$request)
        ->withHeader('Content-Type: application/json')
        ->withHeader('Accept: application/json')
        ->returnResponseArray()
        ->asJsonResponse(true)
        ->get();

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

        return $res_curl['content'];
    }
}
