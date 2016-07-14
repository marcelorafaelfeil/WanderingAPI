<?php

namespace App\Http\Controllers\Account;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Authentication\Credentials;

class MyOrdersController extends Controller
{
	private function __getMyOrders(Request $request) {

	}

	public function index(Request $request) {

		return [];
	}
}
