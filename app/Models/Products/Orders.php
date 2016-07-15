<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;
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
					-> select(['quantidade', 'multiplo', 'preco'])
					-> get()
					-> first();

				if($db) {
					$prds[$k]['limit'] = $db->quantidade;
					$prds[$k]['multiple'] = $db->multiplo;
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
			foreach($prds as $k => $p) {
				$prds[$k]['total_price'] = \Library\Currency::SystemValue($p['total_price'] * $p['quantity']);
			}

			/** Return total of order */
			$total=0;
			foreach($prds as $k=>$p) {
				$total = $p['total_price']+$total;
			}

			$orders=[
				'total_price' => $total,
				'products' => $prds
			];

			var_dump($orders);

			return $orders;
		}
		return false;
	}
}
