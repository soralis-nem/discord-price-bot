<?php

function get_rate(&$USD_JPY)
{
	$url = 'http://api.aoikujira.com/kawase/json/USD';
	$ch = curl_init();
	curl_setopt_array($ch, [CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 5, CURLOPT_CONNECTTIMEOUT => 5, ]);
	$res = json_decode(curl_exec($ch));
	$USD_JPY = $res->JPY;
}