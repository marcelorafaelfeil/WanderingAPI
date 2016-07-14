<?php

namespace App\Models\Authentication;

use Illuminate\Database\Eloquent\Model;

class Passwords extends Model
{
    protected $table = 'login_senhas';
	protected $fillable = ['login_id', 'senha', 'status', 'data_alteracao'];

	public $timestamps = false;
}
