<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
    protected $table = 'imagens';
	protected $fillable = ['src', 'destaque', 'descricao', 'ativo'];
	public $timestamps = false;
}
