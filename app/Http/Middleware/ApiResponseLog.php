<?php

namespace App\Http\Middleware;

use Closure;
use Log;

class ApiResponseLog
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
        $response = $next($request);
        Log::info('[RESPONSE] ' . $response);
        return $response;
    }
}
