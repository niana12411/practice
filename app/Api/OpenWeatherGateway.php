<?php

namespace App\Api;

use App\Exceptions;
use App\Facade\Common;

use Ixudra\Curl\Facades\Curl;
use App\Exceptions\OpenweathermapException;

use Illuminate\Support\MessageBag;


class OpenWeatherGateway
{
    protected array $weatherConfig;
    protected string $appId;
    protected string $apiUrl;
    protected string $lang;

    public function __construct()
    {
        $this->weatherConfig = config('weather.openweathermap');
    }


    /**
     * 驗證配置
     * 
     * @throws OpenweathermapException
     */
    protected function validateConfig(): void
    {
        $this->appId = $this->weatherConfig['appid'];
        $this->apiUrl = $this->weatherConfig['api_url'];
        $this->lang = $this->weatherConfig['lang'];

        if(empty($this->appId)) {
            throw new OpenweathermapException(OpenweathermapException::SETTING_KEY_NOT_FOUND);
        }
        if(empty($this->apiUrl)) {
            throw new OpenweathermapException(OpenweathermapException::SETTING_KEY_NOT_FOUND);
        }
    }

    /**
     * 查詢天氣API V2.5
     * 
     * @param string $country
     * @param string $city
     * @return array
     * @throws OpenweathermapException
     */
    public function getCityWeatherApi(string $country, string $city):array
    {
        //TODO: redis

        $this->validateConfig();
        $request = http_build_query([
            'units' => 'metric',
            'q' => $city.','.$country,
            'appid' => $this->appId,
            'lang' => $this->lang
        ]);

        $res_curl = Curl::to($this->apiUrl.'2.5/weather?'.$request)
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

        return $res_curl['content'];
    }
}
