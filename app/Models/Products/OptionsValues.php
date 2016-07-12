<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

class OptionsValues extends Model
{
    protected $table = 'opcoes_valores';
	protected $fillable = ['descricao', 'preco', 'ativo', 'destaque'];

	public $timestamps = false;
}
