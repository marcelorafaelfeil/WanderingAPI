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
	    } catch (\Exception $e) {
		    return \Response::json([
			    'response' => [
				    'status' => $e->getCode(),
				    'error' => [
					    'message' => $e->getMessage()
				    ]
			    ]
		    ],400);
	    }

	    $Pay = new \PagSeguroPaymentRequest();
    }
}
