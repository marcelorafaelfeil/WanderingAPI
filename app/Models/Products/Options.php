<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

class Options extends Model
{
    protected $table = 'opcoes';
	protected $fillable = ['nome', 'ativo', 'destaque'];

	public $timestamps = false;

	public function values() {
		return $this->belongsToMany('App\Models\Products\OptionsValues', 'opcoes_has_opcoes_valores', 'opcoes_id', 'opcoes_valores_id');
	}
}
