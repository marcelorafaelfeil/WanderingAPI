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

	public static function generateJWT($inf) {
		$header = [
			'alg' => 'HS256',
			'typ' => 'JWT'
		];
		$Iat = new \DateTime();
		$Exp = new \DateTime();
		$Exp->modify('+1 hour');
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
}
