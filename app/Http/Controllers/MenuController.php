<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Contents;

class MenuController extends Controller
{
	private static $TOP_ID = 1;
	private static $FOOTER_ID = 9;


	private function _menu($father) {
		$types = Contents\Types::where('id', '=', $father)
			-> select(['id'])
			-> first();

		$menu = [];
		foreach($types->types()->select(['id','titulo','url','ref'])->get()->all() as $t) {
			$tp=[];
			$tp['id'] = $t->id;
			$tp['titulo'] =  $t->titulo;
			$tp['url'] = $t->url;
			$tp['ref'] = $t->ref;

			if($t->types()->get()->count() > 0) {
				$tp['menu']=[];
				foreach($t->types()->select(['id','titulo','url'])->get()->all() as $sb) {
					$sub['id'] = $sb->id;
					$sub['titulo'] = $sb->titulo;
					$sub['url'] = $sb->url;

					array_push($tp['menu'], $sub);
				}
			}

			array_push($menu, $tp);
		}
		return $menu;
	}

	private function _getTopMenu() {
		return self::_menu(self::$TOP_ID);
	}

	private function _getFooterMenu() {
		return self::_menu(self::$FOOTER_ID);
	}

	public function menu() {
		$menu = array(
			'topo' => self::_getTopMenu(),
			'rodape' => self::_getFooterMenu()
		);

		return $menu;
	}
}
