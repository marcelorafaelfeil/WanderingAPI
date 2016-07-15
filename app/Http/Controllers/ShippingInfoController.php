<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class ShippingInfoController extends Controller
{

    private function __validateShippingCost($request) {
        $error=[];
        if(empty($request->input('zipcode_origin'))) {
            $error[] = [
                'status' => 500,
                'validation' => ['message' => 'Zip Code origin is required.']
            ];
        }
        if(empty($request->input('zipcode_destination'))) {
            $error[] = [
                'status' => 501,
                'validation' => ['message' => 'Zip Code destination is required.']
            ];
        }
        if(empty($request->input('weight'))) {
            $error[] = [
                'status' => 502,
                'validation' => ['message' => 'Weight is required.']
            ];
        }
        if(empty($request->input('length'))) {
            $error[] = [
                'status' => 503,
                'validation' => ['message' => 'Length is required.']
            ];
        }
        if(empty($request->input('height'))) {
            $error[] = [
                'status' => 504,
                'validation' => ['message' => 'Height is required.']
            ];
        }
        if(empty($request->input('width'))) {
            $error[] = [
                'status' => 505,
                'validation' => ['message' => 'Width is required.']
            ];
        }

        return $error;
    }

    public function ShippingCost(Request $request) {
        $validation = self::__validateShippingCost($request);
        if(count($validation) == 0) {
            $params = [
                'tipo' => 'sedex',
                'formato' => 'caixa',
                'cep_origem' => $request->input('zipcode_origin'),
                'cep_destino' => $request->input('zipcode_destination'),
                'peso' => $request->input('weight'),
                'comprimento' => $request->input('length'),
                'altura' => $request->input('height'),
                'largura' => $request->input('width'),
                'diametro' => empty($request->input('diameter')) ? 0 : $request->input('diameter')
            ];

            $shipping[] = \Correios::frete($params);
            $params['tipo'] = 'pac';
            $shipping[] = \Correios::frete($params);

            return \Response::json([
                'response' => [
                    'status' => 401,
                    'success' => [
                        'message' => 'Request accepted.'
                    ],
                    'data' => $shipping
                ]
            ],200);
        } else {
            return \Response::json([
                'response' => [
                    'status' => 400,
                    'error' => [
                        'message' => 'Not possible return information.',
                        'validation' => $validation
                    ]
                ]
            ],400);
        }
    }

    public function Tracking(Request $request) {
        if($request->input('code')) {
            return \Response::json([
                'response' => [
                    'status' => 401,
                    'success' => [
                        'message' => 'Request access.'
                    ],
                    'data' => \Correios::rastrear($request->input('code'))
                ]
            ]);
        } else {
            return \Response::json([
                'response' => [
                    'status' => 400,
                    'error' => [
                        'message' => 'Tracking Code is required.'
                    ]
                ]
            ],400);
        }
    }

    public function AddressFromZipCode(Request $request) {
        if($request->input('zipcode') != "") {
            return \Response::json([
                'response' => [
                    'status' => 401,
                    'success' => [
                        'message' => 'Request accepted.',
                    ],
                    'data' => \Correios::cep($request->input('zipcode'))
                ]
            ]);
        } else {
            return \Response::json([
                'response' => [
                    'status' => 400,
                    'error' => [
                        'message' => 'Zip Code is required.'
                    ]
                ]
            ],400);
        }
    }
}