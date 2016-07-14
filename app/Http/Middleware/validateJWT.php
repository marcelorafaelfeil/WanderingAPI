<?php

namespace App\Http\Middleware;

use Closure;

class validateJWT
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public static function getToken() {
        $headers = getallheaders();
        $auth = $headers['Authorization'];

        if(preg_match('/\bBearer\b/i', $auth)) {
            $auth = preg_replace('/\bBearer\b/i','', $auth);
            return $auth;
        } else {
            return false;
        }
        return false;
    }



    public static function isValid() {
        if(self::JWTExpired()) {
            return false;
        }
    }


    public function handle($request, Closure $next)
    {
        self::isValid();exit;
        if(self::isValid()) {

        } else {
            return response('Unauthorized', 401);
        }
        return $next($request);
    }
}
