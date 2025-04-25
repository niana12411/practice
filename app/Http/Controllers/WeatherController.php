<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Requests\Weather;
use App\Services\WeatherService;

use App\Exceptions;
use App\Facade\Common;

class WeatherController extends Controller
{
    protected $weatherService;
    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }
    public function getIndex(Weather\IndexValidator $request)
    {
        //TODO: 驗證國家與城市        
        // try {
        $res = $this->weatherService->getWeatherData($request);
        $string = '今日天氣'.$res['weather_description'].'，氣溫 '.$res['temp_min'].' ~ '.$res['temp_max'].' 攝氏度。';

        return response()->json($string);
        // } catch (\Throwable $e) {
        //     report($e);
        //     return response()->exception($e);
        // }
    }
}
