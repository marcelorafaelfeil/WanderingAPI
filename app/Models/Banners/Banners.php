<?php

namespace App\Models\Banners;

use Illuminate\Database\Eloquent\Model;

class Banners extends Model
{
    protected $table = 'banners';
	protected $fillable = ['titulo', 'descricao', 'data_inicio', 'data_fim', 'ordem', 'ativo', 'destaque', 'imagens_id'];

	public $timestamps = false;

	public function types() {
		return $this->belongsToMany('App\Models\Banners\Tipos', 'banners_has_tipos_banner', 'banners_id', 'tipos_banner_id');
	}

	public function image() {
		return $this->hasOne('App\Models\Images', 'id', 'imagens_id');
	}
}
