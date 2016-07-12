<?php

namespace App\Models\Banners;

use Illuminate\Database\Eloquent\Model;

class Types extends Model
{
	protected $table = 'tipos_banner';
	protected $fillable = ['nome', 'ref', 'ativo', 'destaque'];

	public $timestamps = false;

	public function banners() {
		return $this->belongsToMany('App\Models\Banners\Banners', 'banners_has_tipos_banner', 'tipos_banner_id', 'banners_id');
	}
}
