<?php

namespace App\Models\Contents;

use Illuminate\Database\Eloquent\Model;

class Contents extends Model
{
    protected $table = 'conteudos';
	protected $fillable = ['titulo', 'url', 'resumo', 'descricao', 'destaque', 'ativo', 'lock'];

	public $timestamps = false;

	public function types() {
		return $this->belongsToMany('App\Models\Contents\Types', 'conteudos_id', 'tipo_conteudos_id');
	}

	public function images() {
		return $this->belongsToMany('App\Models\Images', 'conteudos_id', 'imagens_id');
	}
}
