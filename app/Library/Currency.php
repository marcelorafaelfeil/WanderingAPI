<?php
/**
 * Created by PhpStorm.
 * User: Marcelo
 * Date: 15/07/2016
 * Time: 11:52
 */

namespace Library;


class Currency
{
	private $coin = 'BRL';

	public static function HumanValue($v) {
		$C = new Currency();
		if($C->coin == 'BRL') {
			return number_format($v, 2, ',','.');
		} else if($C->coin == 'USD') {
			return number_format($v, 2, '.',',');
		}
	}

	public static function SystemValue($v) {
		return number_format($v, 3, '.',',');
	}
}