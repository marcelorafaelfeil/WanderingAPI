<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\Products\Products;

class HomeController extends Controller
{
	private $data;
	private function __products() {
		$products = Products::select(['id', 'url', 'resumo', 'preco'])->where('destaque', '=', 1)
			-> where('preco', '>', 0)
			-> get()
			-> all();

		$prds = [];
		foreach($products as $p) {
			$prd['id'] = $p->id;
			$prd['imagem'] = $p->images()->select('src')->where('destaque','=',1)->first()->src;
			$prd['link'] = $p->url;
			$prd['resumo'] = $p->resumo;
			$prd['preco']['decimal'] = $p->preco;
			$prd['preco']['unitario'] = $p->preco;
			$prd['preco']['inteiro'] = $p->preco;
			$prd['estilo']['nome'] = $p->estilo()->select('nome')->first()->nome;
			array_push($prds, $prd);
		}
		return $prds;
	}

	private function getData() {
		$data['produtos'] = self::__products();

		return $data;
	}

    public function index() {
	    return self::getData();
    }
}
