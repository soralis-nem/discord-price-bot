<?php

function get_rate(&$USD_JPY)
{
	$url = 'http://www.gaitameonline.com/rateaj/getrate';
	$ch = curl_init();
	curl_setopt_array($ch, [CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 5, CURLOPT_CONNECTTIMEOUT => 5, ]);
	$res = json_decode(curl_exec($ch));


	foreach ($res->quotes as $quote) {
		switch ($quote->currencyPairCode) {
			case 'USDJPY':
			$USD_JPY = ($quote->ask + $quote->bid) /2;
			break;
		}
	}
}