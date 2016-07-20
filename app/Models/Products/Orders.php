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

	protected $fillable = ['endereco_id', 'cliente_id', 'valor', 'frete', 'status', 'ativo'];

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
			$dim=[];

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
					$prds[$k]['comprimento'] = $db->comprimento;
					$prds[$k]['width'] = $db->largura;
					$prds[$k]['height'] = $db->altura;
					$prds[$k]['price'] = \Library\Currency::SystemValue($db->preco);

					/**
					 * Dimensões
					 */
					if($dim['weight']) {
						$dim['weight'] = $db->peso + $dim['weight'];
					}
					if($dim['width']) {
						$dim['width'] = $db->width + $dim['width'];
					}
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
							$total = $p['price'] * $factor;
							$prds[$k]['total_price'] = \Library\Currency::SystemValue($total);
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

				var_dump($dim);

				$ship=[
					'type' => $orders['shipping']['type'],
					'code' => $orders['shipping']['code'],
					'price' => 14.00,
					'address' => [
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

	public function getOrders() {
		return $this->orders;
	}
}
