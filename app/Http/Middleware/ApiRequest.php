<?php

namespace App\Http\Middleware;

use App\Services\Common\Api\ApiResponse;
use Closure;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Log;

class ApiRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        \App::singleton(ResponseFactory::class, function ($app) {
            return new ApiResponse($app[Factory::class], $app['redirect']);
        });

        Log::info('[REQUEST][parames]' . json_encode($request->all(), JSON_UNESCAPED_UNICODE));
        Log::info('[REQUEST][token] ' . json_encode($request->bearerToken(), JSON_UNESCAPED_UNICODE));
        return $next($request);
    }
}
