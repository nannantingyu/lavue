<?php
namespace App\Http\Middleware;

use Closure;
use Response;
use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EnableCrossDomain extends Middleware{

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

        $allow_origins = explode(",", env('ALLOW_CROSS_DOMAIN'));

        $origin = $request->server('HTTP_ORIGIN') ? $request->server('HTTP_ORIGIN') : '';

        if(in_array($origin, $allow_origins)) {
            $response->header('Access-Control-Allow-Origin', $origin);
            $response->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Cookie, Accept');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, OPTIONS');
            $response->header('Access-Control-Allow-Credentials', 'true');
        }

        return $response;
    }
}
