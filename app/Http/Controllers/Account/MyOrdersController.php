<?php

namespace App\Http\Controllers\Account;

use Faker\Provider\DateTime;
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
			->where('status', '!=', Orders::$STATUS_CANCELED)
			->get();

		if($orders->count() > 0) {
			$ords=[];
			foreach ($orders->all() as $o) {
                $status_text = '';
                switch($o->status) {
                    case 0 : $status_text='Cancelado';break;
                    case 1 : $status_text='Aguardando Pagamento';break;
                    case 2 : $status_text='Pagamento Confirmado';break;
                    case 3 : $status_text='Em Transporte';break;
                    case 4 : $status_text='Entregue ao Destinatári';break;
                    default: $status_text='Aguardando Pagamento';break;
                }
                $date = new \DateTime($o->data);
				$ord=[
					'code' => $o->codigo,
                    'date' => $date->format('d/m/Y').' às '.$date->format('H\hi\m'),
					'value' => [
						'human' => Library\Currency::HumanValue($o->valor),
						'system' => Library\Currency::SystemValue($o->valor)
					],
					'status' => $o->status,
                    'status_text' => $status_text,
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
