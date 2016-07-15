<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class CheckoutController extends Controller
{
    public function index()
    {
	    if ($token_csrf = csrf_token()) {
		    return \Response::json([
			    'response' => [
				    'status' => '002',
				    'success' => [
					    'message' => 'Token successfully generated!'
				    ],
				    'data' => [
					    'token' => $token_csrf
				    ]
			    ]
		    ]);
	    } else {
		    return \Response::json([
			    'response' => [
				    'status' => '001',
				    'error' => [
					    'message' => 'Unable to generate token!'
				    ]
			    ]
		    ], 400);
	    }
    }
}
