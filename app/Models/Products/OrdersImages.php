<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

class OrdersImages extends Model
{
	protected $table = 'prd_pedidos_images';
    protected $fillable = ['prd_pedidos_id', 'src', 'original_src', 'qtd'];
	public $timestamps = false;
}
