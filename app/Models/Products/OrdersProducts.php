<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

class OrdersProducts extends Model
{
    protected $table = 'prd_pedidos';
    protected $fillable = ['produtos_id', 'pedidos_id', 'qtd', 'valor_unitario', 'valor_total', 'num_fotos', 'ativo'];

	public $timestamps = false;

	public function order() {
		return $this->hasOne('App\Models\Products\Order', 'pedidos_id', 'id');
	}

	public function product() {
		return $this->hasOne('App\Models\Products\Products', 'produtos_id', 'id');
	}

	public function options() {
		return $this->belongsToMany('App\Models\Products\OptionsValues', 'prd_pedidos_has_opcoes_valores', 'opcoes_valores_id', 'prd_pedidos_id');
	}

	public function images() {
		return $this->hasMany('App\Models\Products\OrdersImagens', 'prd_pedidos_id');
	}
}
