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
        $auth = isset($headers['Authorization']) ? $headers['Authorization'] : '';

        if($auth) {
            if (preg_match('/\bBearer\b/i', $auth)) {
                $auth = trim(preg_replace('/\bBearer\b/i', '', $auth));
                return $auth;
            }
        }
        return false;
    }

    public static function JWTStructureVerify() {
        if($token = self::getToken()) {
            $t = explode('.', $token);
            $header = $t[0];
            $payload = $t[1];
            $assignature = $t[2];

            if($header) {
                $header = json_encode(base64_decode($header));
            }
            if($payload) {
                $payload = json_encode(base64_decode($payload));
            }
            if($assignature) {
                $assignature = json_encode(base64_decode($assignature));

            }
        }
        return false;
    }

    public static function JWTExpired() {
        if(self::getToken()) {

        } else {
            return true;
        }
    }

    public static function isValid() {
        echo self::JWTStructureVerify();exit;
        if(!self::JWTExpired()) {
            return true;
        }
    }


    public function handle($request, Closure $next)
    {
        self::isValid();exit;
        /*if(self::isValid()) {

        } else {
            return response('Unauthorized', 401);
        }*/
        return $next($request);
    }
}
