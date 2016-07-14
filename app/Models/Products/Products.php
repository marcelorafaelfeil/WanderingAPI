<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    protected $table = 'produtos';
	protected $fillable = ['nome','url','detalhes', 'resumo', 'quantidade', 'multiplo', 'destaque', 'preco', 'estilos_id', 'ativo'];
	public $timestamps = false;

	public function style() {
		return $this->hasOne('App\Models\Products\Styles', 'id');
	}

	public function images() {
		return $this->belongsToMany('App\Models\Images', 'produtos_has_imagens', 'produtos_id', 'imagens_id');
	}

	public function options() {
		return $this->hasMany('App\models\Products\Options', 'produtos_id', 'id');
	}

	public function banners() {
		return $this->belongsToMany('App\Models\Banners\Banners', 'produtos_has_banners', 'produtos_id', 'banners_id');
	}
}
