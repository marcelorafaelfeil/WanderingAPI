<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\Products\Products;

class OptionsController extends Controller
{
    public function ofProduct($url) {
	    $product = Products::where('url', '=', $url)
	        -> select(['id','nome'])
	        -> get();

	    if($product->count() > 0) {
		    $product = $product->first();

		    $json=[];

		    $json['name'] = $product->nome;

		    if($product->options()) {
			    $json['options']=[];

			    foreach($product->options()->get()->all() as $o) {
				    $opt = array(
					    'name' => $o->nome,
					    'values' => []
				    );

				    if($o->values()) {
					    foreach($o->values()->get()->all() as $v) {
						    $val=array(
							    'description' => $v->descricao,
							    'price' => [
								    'human' => number_format($v->preco,2,',','.'),
								    'system' => $v->preco
							    ]
						    );

						    if($v->image) {
							    $val['image'] = $v->image->src;
						    }
						    array_push($opt['values'],$val);
					    }
				    }

				    array_push($json['options'],$opt);
			    }
		    }

		    return \Response::json([
			    'response' => [
				    'status' => 1,
				    'success' => [
					    'message' => 'Product found!'
				    ],
				    'data' => $json
			    ]
		    ], 200);
	    } else {
		    return \Response::json([
			    'response' => [
				    'status' => 0,
				    'error' => [
					    'message' => 'Product not found!'
				    ]
			    ]
		    ],204);
	    }
    }
}
