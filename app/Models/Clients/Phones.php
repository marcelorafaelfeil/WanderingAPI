<?php

namespace App\Models\Clients;

use Illuminate\Database\Eloquent\Model;

class Phones extends Model
{
    protected $table = 'telefones';
	protected $fillable = ['clientes_id', 'ddd', 'telefone', 'principal'];

	public $timestamps = false;

	public function client() {
		return $this->hasOne('App\Models\Clients\Clients', 'id', 'clientes_id');
	}
}
