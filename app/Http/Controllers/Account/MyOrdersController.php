<?php

namespace App\Http\Controllers\Account;

use Illuminate\Http\Request;
use Library;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Authentication\Credentials;
use App\Models\Products\Orders;

class MyOrdersController extends Controller
{
	private function __getMyOrders() {
		$credentials = Credentials::where('email',Credentials::getUser())->get()->first();
		$clients = $credentials->client()->get()->first();
		$orders = $clients->orders()
			->where('status', Orders::$STATUS_OPEN)
			->get();

		if($orders->count() > 0) {
			$ords=[];
			foreach ($orders->all() as $o) {
				$ord=[
					'code' => $o->codigo,
					'value' => [
						'human' => Library\Currency::HumanValue($o->valor),
						'system' => Library\Currency::SystemValue($o->valor)
					],
					'status' => $o->status,
					'number_of_products' => $o->products()->get()->count()
				];
				array_push($ords, $ord);
			}

			return \Response::json([
				'response' => [
					'status' => 101,
					'success' => [
						'message' => 'Orders found!'
					],
					'data' => $ords
				]
			]);
		} else {
			return \Response::json([
				'response' => [
					'status' => 100,
					'error' => [
						'message' => 'Orders not found!'
					]
				]
			],204);
		}
	}

	public function index() {
		return self::__getMyOrders();
	}
}
