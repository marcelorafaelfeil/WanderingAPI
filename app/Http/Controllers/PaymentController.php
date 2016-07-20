<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Products\Orders;

class PaymentController extends Controller
{
    public function PagSeguro(Request $request) {
	    $Orders = new Orders();
	    try {
		    $Orders->setOrders($request->input('orders'));

		    $PayRequest = new \PagSeguroPaymentRequest();

		    $orders = $Orders->getOrders();

		    foreach($orders['products'] as $p) {
			    $PayRequest->addItem($p['code'], $p['name'], $p['quantity'], $p['total_price']);
		    }

		    // Tipo de envio pelos correios
		    $PayRequest->setShippingType($orders['shipping']['code']);
		    $PayRequest->setShippingCost($orders['shipping']['price']);
		    // Endereço de entrega
		    $addr = $orders['shipping']['address'];
		    $PayRequest->setShippingAddress(
			    $addr['cep'],
			    $addr['street'],
			    $addr['number'],
			    $addr['complement'],
			    $addr['city'],
			    $addr['state'],
			    $addr['country']
		    );
		    // Dados da pessoa que está comprando
		    $cli = $orders['client'];
		    $PayRequest->setSender(
			    $cli['name'],
			    $cli['email'],
			    $cli['areacode'],
			    $cli['phone'],
		        'CPF',
			    $cli['cpf']
		    );
		    // Referência do pedido
		    $PayRequest->setReference('8UR892');
		    // URL de Retorno
		    $PayRequest->setRedirectURL($orders['redirect_url']);
		    // País
		    $PayRequest->setCurrency('BRL');

		    // Envia a requisição
		    try {
			    /*$credentials = \PagSeguroConfig::getAccountCredentials();
			    $checkout = $PayRequest->register($credentials);*/
			    $checkout = 'teste';
			    return \Response::json([
				    'response' => [
					    'status' => 603,
					    'data' => [
						    'checkout_url' => $checkout,
						    'orders' => $orders
					    ]
				    ]
			    ],200);
		    } catch (\PagSeguroServiceException $e) {
			    return \Response::json([
				    'response' => [
					    'status' => 601,
					    'error' => [
						    'message' => $e->getMessage()
					    ]
				    ]
			    ],400);
		    }

		    return $Orders->getOrders();
	    } catch (\Exception $e) {
		    return \Response::json([
			    'response' => [
				    'status' => $e->getCode(),
				    'error' => [
					    'message' => $e->getMessage()
				    ]
			    ]
		    ], 400);
	    }
    }
}
