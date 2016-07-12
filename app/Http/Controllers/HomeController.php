<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\Products\Products;
use App\Models\Banners;

class HomeController extends Controller
{
	private $data;
	private function __getProducts() {
		$products = Products::select(['id', 'nome', 'url', 'resumo', 'preco'])->where('destaque', '=', 1)
			-> where('preco', '>', 0)
			-> get()
			-> all();

		$prds = [];
		foreach($products as $p) {
			$prd['id'] = $p->id;
			$prd['nome'] = $p->nome;
			$prd['link'] = $p->url;
			$prd['resumo'] = $p->resumo;

			$price = number_format($p->preco, 2, ',','.');
			$price_explode = explode(',',$price);

			$prd['preco']['decimal'] = $price_explode[0];
			$prd['preco']['unitario'] = $price_explode[1];
			$prd['preco']['inteiro'] = $price;

			if($p->estilo()->select('nome')->first()) {
				$prd['estilo']['nome'] = $p->estilo()->select('nome')->first()->nome;
			}
			if($p->images()->first()) {
				$prd['imagem'] = $p->images()->select('src')->where('destaque','=',1)->first()->src;
			}

			array_push($prds, $prd);
		}
		return $prds;
	}

	private function __getBanners() {
		$banners = Banners\Types::select('id')
			-> with(['banners' => function($query){
				$query
					-> with ('image');
			}])
			-> get()
			-> first();

		$bans=[];
		foreach($banners->banners()->get()->all() as $b) {
			$ban['id'] = $b->id;
			$ban['titulo'] = $b->titulo;
			$ban['link'] = $b->link;
			$ban['target'] = $b->target;
			if($b->image()->get()->first()) {
				$ban['imagem'] = $b->image()->get()->first()->src;
			}
			array_push($bans, $ban);
		}
		return $bans;
	}

	private function getData() {
		$data['produtos'] = self::__getProducts();
		$data['banners'] = self::__getBanners();

		return $data;
	}

    public function index() {
	    return self::getData();
    }
}
