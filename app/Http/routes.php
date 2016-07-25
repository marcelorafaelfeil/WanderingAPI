<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(['prefix' => 'api'], function(){
	Route::get('home', 'HomeController@home');
	Route::get('menu', 'MenuController@menu');
	Route::get('product/{url}', 'ProductsController@details');
	Route::get('option/{url}', 'OptionsController@ofProduct');

	Route::group(['prefix' => 'authentication'], function() {
		Route::get('', 'Authentication\AuthController@login');
		Route::post('process', 'Authentication\AuthController@validation');
	});

	Route::group(['prefix' => 'checkout', 'middleware' => 'jwt'], function() {
		Route::get('/', 'CheckoutController@index');
	});

	Route::group(['prefix' => 'correios'], function(){
		Route::get('shipping', 'ShippingInfoController@ShippingCost');
		Route::get('address', 'ShippingInfoController@AddressFromZipCode');
		Route::get('tracking', 'ShippingInfoController@Tracking');
	});

	Route::group(['prefix' => 'payment'], function(){
		Route::post('PagSeguro', 'PaymentController@PagSeguro');
	});

	Route::group(['prefix' => 'account', 'middleware' => 'jwt'], function() {
		Route::get('', 'Account\MyOrdersController@index');
	});
});

Route::get('images/{class}/{key}/{filename}', function ($class, $key, $filename)
{
	$kd = str_split($key);
	$keydir="";
	$i=0;
	foreach($kd as $k) {
		$i++;
		$keydir.=$k;
		if(count($kd) > $i) {
			$keydir.='/';
		}
	}
	$path = storage_path() . '/uploads/' . $class. '/'. $keydir . '/' . $filename;

	return Image::make($path)->response();
});