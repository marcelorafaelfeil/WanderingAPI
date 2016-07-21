<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;
use App\Models\Authentication\Credentials;
use App\Models\Clients\Clients;
// use App\Models\Products\Products;

class Orders extends Model
{
    protected $table = 'pedidos';
	private $orders = [];

	public static $STATUS_OPEN = 1;

	protected $fillable = ['endereco_id', 'cliente_id', 'codigo', 'valor', 'frete', 'status', 'ativo'];

	public $timestamps = false;

	public function products() {
		return $this->hasMany('App\Models\Products\OrdersProducts', 'pedidos_id', 'id');
	}

	public function address() {
		return $this->hasOne('App\Models\Clients\Addresses', 'endereco_id', 'id');
	}

	public function client() {
		return $this->hasOne('App\Models\Clients\Clients', 'id', 'cliente_id');
	}

	public function setOrders($orders) {
		if(count($orders) > 0) {
			$prds=[];
			$peso=0;
			$total=0;
			$dim=[
				'weight' => 0,
				'width' => 0,
				'height' => 0,
				'diameter' => 0,
				'length' => 0
			];

			if ($orders['products']) {
				foreach ($orders['products'] as $p) {
					if(empty($p['id'])) throw new \Exception('Has ghost products.',600);
					if(empty($p['quantity'])) throw new \Exception('Quantity product is invalid.',600);
					if(empty($p['images'])) throw new \Exception('Product '.$p['id'].' not have images selected.',600);
					$prd = [
						'id' => $p['id'],
						'quantity' => $p['quantity'],
						'images' => $p['images']
					];
					if(isset($p['option'])) {
						$prd['option'] = $p['option'];
					}
					array_push($prds, $prd);
				}
			}

			foreach($prds as $k => $p) {
				$db = Products::where('id', $p['id'])
					-> select(['codigo', 'nome', 'quantidade', 'multiplo', 'preco', 'peso', 'diametro', 'comprimento', 'largura', 'altura'])
					-> get()
					-> first();

				if($db) {
					$peso = $db->peso+$peso;
					$prds[$k]['name'] = $db->nome;
					$prds[$k]['code'] = $db->codigo;
					$prds[$k]['limit'] = $db->quantidade;
					$prds[$k]['multiple'] = $db->multiplo;
					$prds[$k]['weight'] = $db->peso;
					$prds[$k]['diameter'] = $db->diametro;
					$prds[$k]['length'] = $db->comprimento;
					$prds[$k]['width'] = $db->largura;
					$prds[$k]['height'] = $db->altura;
					$prds[$k]['price'] = \Library\Currency::SystemValue($db->preco);
				}
			}


			// Count number of images selected
			foreach($prds as $k => $p) {
				$total_images=0;
				foreach ($p['images'] as $img) {
					$total_images = $img['quantity'] + $total_images;
				}
				$prds[$k]['total_images'] = $total_images;
			}

			/*
			 * Calculate the price from quantity
			 * of images case multiple is true
			 */
			foreach($prds as $k => $p) {
				if($p['multiple']) {
					if($p['total_images'] < $p['limit']) {
						throw new \Exception('You should select more photos in product '.$p['id'], 600);
					} else {
						$factor = round($p['total_images'] / $p['limit']);
						if ($factor == ($p['total_images'] / $p['limit'])) {
							$subtotal = $p['price'] * $factor;

							$prds[$k]['total_price'] = \Library\Currency::SystemValue($subtotal);
						} else {
							throw new \Exception('You should select more or less photos in product '.$p['id'], 600);
						}
					}
				} else {
					$prds[$k]['total_price'] = $p['price'];
					if($p['total_images'] > $p['limit']) {
						throw new \Exception('Burst limit images in product '.$p['id'], 600);
					}
				}

				$total = ($prds[$k]['total_price'] * $prds[$k]['quantity']) + $total;

				/**
				 * Dimensões
				 */
				if($p['weight']) {
					$weight = ($p['weight'] * $p['quantity']);
					if($p['multiple']) {
						$weight = ($weight*$factor) + $dim['weight'];
					} else {
						$weight = $weight + $dim['weight'];
					}
					$dim['weight'] = $weight;
				}
				if($dim['width'] < $p['width']) {
					$dim['width'] = $p['width'];
				}
				if($p['height']) {
					$height = ($p['height'] * $p['quantity']);
					if($p['multiple']) {
						$height = ($height*$factor) + $dim['height'];
					} else {
						$height = $height + $dim['height'];
					}
					$dim['height'] = $height;
				}
				if($dim['diameter'] < $p['diameter']) {
					$dim['diameter'] = $p['diameter'];
				}
				if($dim['length'] < $p['length']) {
					$dim['length'] = $p['length'];
				}
			}

			/**
			 * Update price from quantity
			 */
			/*foreach($prds as $k => $p) {
				$prds[$k]['total_price'] = \Library\Currency::SystemValue($p['total_price'] * $p['quantity']);
			}*/

			/**
			 * Client Information
			 */
			$Credentials = Credentials::where('email', Credentials::getUser())
				-> select([
					'id'
				])
				-> get()
				-> first();
			$Client = $Credentials -> client()
				-> get()
				-> first();

			if(!$Client) {
				throw new \Exception('Client is not found.', 600);
			} else {
				$Phone = $Client->phones()->where('principal', 1)->get()->first();
				$cli=[
					'code' => $Client->id,
					'name' => $Client->nome,
					'email' => Credentials::getUser(),
					'areacode' => $Phone->ddd,
					'phone' => $Phone->telefone,
					'cpf' => $Client->cpf
				];
			}

			/**
			 * Shipping information
			 */
			if(isset($orders['shipping']) && $orders['shipping']['type'] && $orders['shipping']['code'] && $orders['shipping']['address']) {
				$address = $Client
					-> addresses()
					-> where('id',$orders['shipping']['address'])
					-> get()
					-> first();


				if($orders['shipping']['type'] == 1) {
					$params=[
						'formato' => 'caixa',
						'cep_origem' => '85859370',
						'cep_destino' => $address->cep,
						'peso' => $dim['weight'],
						'comprimento' => $dim['length'],
						'altura' => $dim['height'],
						'largura' => $dim['width'],
						'diametro' => $dim['diameter']
					];
					if($orders['shipping']['code'] == 1) {
						$params['tipo'] = 'pac';
					} else {
						$params['tipo'] = 'sedex';
					}
				}
				$frete = \Correios::frete($params);
				$total = $total+$frete['valor'];
				$ship=[
					'code' => $orders['shipping']['code'],
					'price' => $frete['valor'],
					'address' => [
						'code' => $orders['shipping']['address'],
						'cep' => $address->cep,
						'street' => $address->logradouro,
						'number' => $address->numero,
						'complement' => $address->complemento,
						'city' => $address->cidade,
						'state' => $address->estado,
						'country' => 'BRA'
					]
				];
			} else {
				throw new \Exception('Shipping is required.', 600);
			}

			// Redirect URL
			$orders=[
				'code' => strtoupper(str_random(20)),
				'total_price' => $total,
				'products' => $prds,
				'shipping' => $ship,
				'client' => $cli,
				'redirect_url' => $orders['redirect_url']
			];

			$this->orders = $orders;

			return true;
		}
		return false;
	}

	public static function CreateOrder($orders) {
		try {
			$order = Orders::create([
				'endereco_id' => $orders['shipping']['address']['code'],
				'cliente_id' => $orders['client']['code'],
				'codigo' => $orders['code'],
				'valor' => $orders['total_price'],
				'frete' => $orders['shipping']['price'],
				'status' => 1,
			]);
		} catch (\Exception $e) {
			throw new \Exception('Error on save the order, into database.');
		}

		foreach($orders['products'] as $p) {
			try {
				$prd = OrdersProducts::create([
					'produtos_id' => $p['id'],
					'pedidos_id' => $order->id,
					'qtd' => $p['quantity'],
					'valor_unitario' => $p['price'],
					'valor_total' => $p['total_price'],
					'num_fotos' => $p['total_images']
				]);
			} catch (\Exception $e) {
				throw new \Exception('Error on save the products, into database.');
			}
			try {
				if ($p['images']) {
					foreach ($p['images'] as $img) {
						OrdersImages::create([
							'prd_pedidos_id' => $prd->id,
							'src' => $img['src'],
							'original_src' => $img['original_src'],
							'qtd' => $img['quantity']
						]);
					}
				}
			} catch (\Exception $e) {
				throw new \Exception('Error on save the images, into database.');
			}
		}
	}

	public function getOrders() {
		return $this->orders;
	}
}
