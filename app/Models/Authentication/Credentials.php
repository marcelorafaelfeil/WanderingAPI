<?php

namespace App\Models\Authentication;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class Credentials extends Model
{
    protected $table = 'login';
	protected $fillable = ['email', 'email_token', 'nivel', 'ativo', 'data_ultimo_login'];
	public $timestamps = false;

	public function passwords() {
		return $this->hasMany('App\Models\Authentication\Passwords', 'login_id', 'id');
	}

	public function client() {
		return $this->hasOne('App\Models\Clients\Clients', 'login_id', 'id');
	}

	public static function generateJWT($inf) {
		$header = [
			'alg' => 'HS256',
			'typ' => 'JWT'
		];
		$Iat = new \DateTime();
		$Exp = new \DateTime();
		$Exp->modify('+1 hours');
		$payload = [
			'iss' => env('APP_URL'),
			'iat' => $Iat->getTimestamp(),
			'exp' => $Exp->getTimestamp(),
			'email' => $inf['email']
		];

		$header = base64_encode(json_encode($header));
		$payload = base64_encode(json_encode($payload));

		$assignature = hash_hmac('sha256', $header.'.'.$payload, env('APP_KEY'), true);
		$assignature = base64_encode($assignature);

		$jwt = $header.'.'.$payload.'.'.$assignature;

		return $jwt;
	}

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

	public static function getUser() {
		$payload = self::getJWTPayload();

		return $payload->email;
	}
}
