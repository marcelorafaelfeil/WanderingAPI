<?php

namespace App\Models\Contents;

use Illuminate\Database\Eloquent\Model;

class Types extends Model
{
    protected $table = 'tipo_conteudos';
	protected $fillable = ['ord', 'id_pai', 'titulo', 'url', 'resumo', 'destaque', 'lock', 'ativo'];

	public $timestamps = false;

	public function father() {
		return $this->hasOne('App\Models\Contents\Types', 'id_pai', 'id');
	}

	public function types() {
		return $this->belongsTo('App\Models\Contents\Types', 'id', 'id_pai');
	}

	public function contents() {
		return $this->belongsToMany('App\Models\Contents\Contents', 'tipo_conteudos_id', 'conteudos_id');
	}
}