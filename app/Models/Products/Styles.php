<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

class Styles extends Model
{
    protected $table = 'estilos';
	protected $fillable = ['nome', 'num_fotos', 'ativo'];
	public $timestamps = false;

	public function products() {
		return $this->hasMany('App\Models\Products\Products', 'estilos_id');
	}
}
