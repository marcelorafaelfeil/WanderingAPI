<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products\Products;

use App\Http\Requests;

class ProductsController extends Controller
{
	public function details($url) {
		$product = Products::select(['id', 'nome', 'url', 'resumo', 'detalhes', 'preco'])
			-> where('url', '=', $url)
			-> get();

		if($product->count() > 0) {
			$json=[];
			$product = $product->first();

			$json['titulo'] = $product->nome;
			$json['resumo'] = $product->resumo;
			$json['url'] = $product->url;
			$json['detalhes'] = $product->detalhes;

			$preco = number_format($product->preco, 2, ',','.');
			$p = explode(',',$preco);

			$decimal = $p[0];
			$unitario = $p[1];

			$json['precos'] = array(
				'inteiro' => $product->preco,
				'decimal' => $decimal,
				'unitario' => $unitario
			);

			if($product->options()->count() > 0) {
				$json['opcoes'] = true;
			}

			if($product->style) {
				$json['estilo']['chave'] = $product->style->chave;
			}

			if($product->banners()->count() > 0) {
				$json['banners'] = [];
				foreach($product->banners()->get()->all() as $b) {
					$banner=array(
						'image' => $b->image->src
					);
					array_push($json['banners'],$banner);
				}
			}

			if($product->images()->count() > 0) {
				$json['imagens'] = [];
				foreach($product->images()->get()->all() as $i) {
					$img = array(
						'imagem' => $i->src
					);
					array_push($json['imagens'], $img);
				}
			}

			return \Response::json($json,200);
		} else {
			return \Response::json([
				'response' => [
					'message' => 'Product not found!'
				]
			],204);
		}
	}
}
