<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    protected $table = 'produtos';
	protected $fillable = ['nome','url','detalhes', 'resumo', 'quantidade', 'multiplo', 'destaque', 'preco', 'estilos_id', 'ativo'];
	public $timestamps = false;

	public function estilo() {
		return $this->hasOne('App\Models\Products\Styles', 'id');
	}

	public function images() {
		return $this->belongsToMany('App\Models\Images', 'produtos_has_imagens', 'produtos_id', 'imagens_id');
	}

	public function options() {
		return $this->belongsToMany('App\models\Products\Options', 'produtos_has_opcoes', 'produtos_id', 'opcoes_id');
	}
}
