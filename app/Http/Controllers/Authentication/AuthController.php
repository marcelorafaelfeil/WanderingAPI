<?php

namespace App\Http\Controllers\Authentication;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Authentication\Credentials;

class AuthController extends Controller
{
	public function login() {
		if($token_csrf = csrf_token()) {
			return \Response::json([
				'response' => [
					'status' => 1,
					'success' => [
						'message' => 'Token generate success!'
					],
					'data' => [
						'auth_token' => $token_csrf
					]
				]
			],200);
		} else {
			return \Response::json([
				'response' => [
					'status' => 0,
					'error' => [
						'message' => 'Unable to generate token!'
					]
				]
			]);
		}
	}

    public function validation(Request $request) {
	    if($request->input('_token')) {
		    if ($request->input('email') && $request->input('pass')) {
			    $credentials = Credentials::select('id')
				    ->where('email', '=', $request->input('email'))
				    ->get();
			    if ($credentials->count() > 0) {
				    $credentials = $credentials->first();

				    $password = $credentials
					    ->passwords()
					    ->where('status', '=', 1)
					    ->get();

				    if ($password->count() > 0) {
					    if (\Crypt::decrypt($password->first()->senha) == $request->input('pass')) {
						    return \Response::json([
							    'response' => [
								    'status' => 1,
								    'success' => [
									    'message' => 'Credentials accepted.'
								    ],
								    'data' => [
									    'access_token' => Credentials::generateJWT(['email' => $request->input])
								    ]
							    ]
						    ], 200);
					    } else {
						    return \Response::json([
							    'response' => [
								    'status' => 0,
								    'error' => [
									    'message' => 'Invalid password'
								    ]
							    ]
						    ], 400);
					    }
				    } else {
					    return \Response::json([
						    'response' => [
							    'status' => 0,
							    'error' => [
								    'message' => 'Dont have password related to this user.'
							    ]
						    ]
					    ], 409);
				    }
			    } else {
				    return \Response::json([
					    'response' => [
						    'status' => 0,
						    'error' => [
							    'message' => 'Invalid credentials.'
						    ]
					    ]
				    ], 400);
			    }
		    } else {
			    return \Response::json([
				    'response' => [
					    'status' => 0,
					    'error' => [
						    'message' => 'Required credentials.'
					    ]
				    ]
			    ], 400);
		    }
	    } else {
		    return \Response::json([
			    'response' => [
				    'status' => -1,
				    'error' => [
					    'message' => 'Unauthorized request!'
				    ]
			    ]
		    ],403);
	    }
    }
}
