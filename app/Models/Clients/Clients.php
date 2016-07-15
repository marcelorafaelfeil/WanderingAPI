<?php

namespace App\Models\Clients;

use Illuminate\Database\Eloquent\Model;

class Clients extends Model
{
    protected $table = 'clientes';
	protected $fillable = ['login_id', 'nome', 'nascimento', 'cpf', 'ativo'];

	public $timestamps = false;

	public function credential() {
		return $this->hasOne('App\Models\Authentication\Credentials', 'id', 'login_id');
	}

	public function addresses() {
		return $this->hasMany('App\Models\Clients\Addresses', 'clientes_id', 'id');
	}

	public function phones() {
		return $this->hasMany('App\Models\Clients\Phones', 'clientes_id', 'id');
	}

	public function orders() {
		return $this->hasMany('App\Models\Products\Orders', 'cliente_id', 'id');
	}
}
