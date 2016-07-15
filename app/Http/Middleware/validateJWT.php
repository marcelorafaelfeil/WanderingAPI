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
            // Explode in token
            $t = explode('.', $token);
            $header = $t[0];
            $payload = $t[1];
            $assignature = $t[2];

            if($assignature && $header && $payload) {
                // Generate secret toke to compare
                $token = hash_hmac('sha256', $header.'.'.$payload, env('APP_KEY'), true);
                $token = base64_encode($token);
                if($token == $assignature) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function getJWTPayload() {
        if($token = self::getToken()) {
            $t = explode('.', $token);
            $payload = $t[1];

            if($payload) {
                $payload = json_decode(base64_decode($payload));

                return $payload;
            }
        }
        return false;
    }

    public static function JWTExpired() {
        if($p = self::getJWTPayload()) {
            $exp = date('Y-m-d H:i:s',$p->exp);
            $Exp = new \DateTime($exp);
            $Now = new \DateTime();

            if($Exp > $Now) {
                return false;
            }
        }
        return true;
    }

    public static function isValid() {
        if(self::JWTStructureVerify() && !self::JWTExpired()) {
            return true;
        }
        return false;
    }


    public function handle($request, Closure $next)
    {
        if(!self::isValid()) {
            return \Response::json([
                'response' => [
                    'status' => 306,
                    'error' => [
                        'message' => 'Token expired!'
                    ]
                ]
            ], 401);
        }
        return $next($request);
    }
}
