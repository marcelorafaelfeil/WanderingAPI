<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\Products\Products;
use App\Models\Banners;
use App\Models\Contents\Contents;

class HomeController extends Controller
{
	private static $ID_TEXT = 1;
	private static $ID_ABOUT = 2;

	private $data;
	private function __getProducts() {
		$products = Products::select(['id', 'nome', 'url', 'resumo', 'preco'])->where('destaque', '=', 1)
			-> where('preco', '>', 0)
			-> get()
			-> all();

		$prds = [];
		foreach($products as $p) {
			$resumo='';
			if(strlen($p->resumo) > 150){
				$resumo = substr($p->resumo,0,150).'...';
			} else {
				$resumo = $p->resumo;
			}
			$prd['id'] = $p->id;
			$prd['nome'] = $p->nome;
			$prd['link'] = $p->url;
			$prd['resumo'] = $resumo;

			$price = number_format($p->preco, 2, ',','.');
			$price_explode = explode(',',$price);

			$prd['preco']['decimal'] = $price_explode[0];
			$prd['preco']['unitario'] = $price_explode[1];
			$prd['preco']['inteiro'] = $price;

			if($p->style()->select('nome')->first()) {
				$prd['estilo']['nome'] = $p->style()->select('nome')->first()->nome;
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

	private function __getContent($id) {
		$c = Contents::where('id', '=', $id)
			-> select(['titulo', 'descricao'])
			-> first();

		$content=[];
		if($c->get()->count()){
			$content['titulo'] = $c->titulo;
			$content['descricao'] = $c->descricao;
		}

		return $content;
	}

	private function __getText() {
		return self::__getContent(self::$ID_TEXT);
	}

	private function __getAbout() {
		return self::__getContent(self::$ID_ABOUT);
	}

	private function getHomeData() {
		$data['produtos'] = self::__getProducts();
		$data['banners'] = self::__getBanners();
		$data['texto'] = self::__getText();
		$data['sobrenos'] = self::__getAbout();

		return $data;
	}

    public function home() {
	    if(count(self::getHomeData()) == 0) {
		    return \Response::json([],204);
	    } else {
		    return \Response::json(self::getHomeData(),200);
	    }
    }
}
