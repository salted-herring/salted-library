<?php
/**
 * @file Currency
 *
 * Generic Currency functions
 * */
namespace SaltedHerring;
class Currency {
	public static function exchange($amount, $from = 'NZD', $to = 'CNY'){
		$url = "http://www.google.com/finance/converter?a=$amount&from=$from&to=$to"; 
		if ($data = RPC::fetch($url)) {
			$dom = new \DOMDocument;
			@$dom->loadHTML($data);
			$finder = new \DomXPath($dom);
			$classname="bld";
			$nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
			$node = $nodes->item(0)->nodeValue;
			$amount = str_replace(' CNY', '', $node);
			
			return number_format($amount, 2, '.', ',');
		}
		return false;
	} 
}
