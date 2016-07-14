<?php

namespace App\Models\Clients;

use Illuminate\Database\Eloquent\Model;

class Addresses extends Model
{
	protected $table = 'enderecos';
	protected $fillable = ['clientes_id', 'logradouro', 'numero', 'bairro', 'cidade', 'estado', 'cep', 'ativo', 'principal'];

	public $timestamps = false;

	public function client() {
		return $this->hasOne('App\Models\Clients\Clients', 'clientes_id', 'id');
	}
}
