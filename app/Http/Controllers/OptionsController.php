<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\Products\Products;

class OptionsController extends Controller
{
    public function ofProduct($url) {
	    $product = Products::where('url', '=', $url)
	        -> select(['id','nome', 'url', 'quantidade', 'organizar', 'multiplo', 'preco'])
	        -> get();

	    if($product->count() > 0) {
		    $product = $product->first();

		    $json=[];

		    $json['id'] = $product->id;
		    $json['name'] = $product->nome;
		    $json['url'] = $product->url;
		    $json['limit'] = $product->quantidade;
		    $json['organize'] = $product->organizar;
		    $json['multiple'] = $product->multiplo;
		    $json['style'] = $product->style->chave;
		    if($product->images()->first()) {
			    $json['image'] = $product->images()->orderBy('destaque','DESC')->first()->src;
		    }

		    if($product->options()->first()) {
			    $option = $product->options()->first();
			    $json['options']=[];
			    foreach($option->values()->orderBy('id', 'asc')->get()->all() as $v) {
				    $val=array(
					    'id' => $v->id,
					    'description' => $v->descricao,
					    'image' => $v->image->src,
					    'price' => [
						    'human' => number_format($v->preco,2,',','.'),
						    'system' => $v->preco
					    ]
				    );
				    array_push($json['options'], $val);
			    }
			    /*foreach($product->options()->get()->all() as $o) {
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
			    }*/
		    } else {
			    $json['price']= [
				    'system' => \Library\Currency::SystemValue($product->preco),
				    'human' => \Library\Currency::HumanValue($product->preco)
			    ];
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