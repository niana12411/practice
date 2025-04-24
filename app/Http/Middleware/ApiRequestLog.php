<?php

namespace App\Http\Middleware;

use Closure;
use Log;

class ApiRequestLog
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
        Log::info('[REQUEST][parames]' . json_encode($request->all(), JSON_UNESCAPED_UNICODE));
        Log::info('[REQUEST][token] ' . json_encode($request->bearerToken(), JSON_UNESCAPED_UNICODE));
        return $next($request);
    }
}
