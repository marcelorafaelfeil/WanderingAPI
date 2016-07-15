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
					'status' => '002',
					'success' => [
						'message' => 'Token successfully generated!'
					],
					'data' => [
						'token' => $token_csrf
					]
				]
			],200);
		} else {
			return \Response::json([
				'response' => [
					'status' => '001',
					'error' => [
						'message' => 'Unable to generate token!'
					]
				]
			],400);
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
								    'status' => 305,
								    'success' => [
									    'message' => 'Credentials accepted.'
								    ],
								    'data' => [
									    'access_token' => Credentials::generateJWT(['email' => $request->input('email')])
								    ]
							    ]
						    ], 200);
					    } else {
						    return \Response::json([
							    'response' => [
								    'status' => 304,
								    'error' => [
									    'message' => 'Invalid password'
								    ]
							    ]
						    ], 400);
					    }
				    } else {
					    return \Response::json([
						    'response' => [
							    'status' => 303,
							    'error' => [
								    'message' => 'Dont have password related to this user.'
							    ]
						    ]
					    ], 409);
				    }
			    } else {
				    return \Response::json([
					    'response' => [
						    'status' => 302,
						    'error' => [
							    'message' => 'Invalid credentials.'
						    ]
					    ]
				    ], 400);
			    }
		    } else {
			    return \Response::json([
				    'response' => [
					    'status' => 301,
					    'error' => [
						    'message' => 'Required credentials.'
					    ]
				    ]
			    ], 400);
		    }
	    } else {
		    return \Response::json([
			    'response' => [
				    'status' => 300,
				    'error' => [
					    'message' => 'Unauthorized request!'
				    ]
			    ]
		    ],403);
	    }
    }
}
