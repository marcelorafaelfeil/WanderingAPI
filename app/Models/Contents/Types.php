<?php

namespace App\Models\Contents;

use Illuminate\Database\Eloquent\Model;

class Types extends Model
{
    protected $table = 'tipo_conteudos';
	protected $fillable = ['ord', 'id_pai', 'titulo', 'url', 'resumo', 'destaque', 'lock', 'ativo'];

	public $timestamps = false;

	public function contents() {
		return $this->belongsToMany('App\Models\Contents\Contents', 'tipo_conteudos_id', 'conteudos_id');
	}
}
