<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $table = 'pedidos';
	protected $fillable = ['endereco_id', 'cliente_id', 'valor', 'frete', 'status', 'ativo'];

	public $timestamps = false;

	public function products() {
		return $this->hasMany('App\Models\Products\OrdersProducts', 'pedidos_id', 'id');
	}

	public function address() {
		return $this->hasOne('App\Models\Clients\Addresses', 'endereco_id', 'id');
	}

	public function client() {
		return $this->hasOne('App\Models\Clients\Clients', 'id', 'cliente_id');
	}
}
