<?php

$cmd_client->registerCommand('xem', function ($message) {
    $urls = [];
    $urls['ZA'] = 'https://api.zaif.jp/api/1/ticker/xem_jpy'; // Zaif
    $urls['ZAB'] = 'https://api.zaif.jp/api/1/ticker/xem_btc'; // Zaif
    $urls['PX'] = 'https://poloniex.com/public?command=returnTicker'; //Poloniex
    $urls['CC'] = 'https://coincheck.com/api/ticker';
    $mh = curl_multi_init();
    $chs = [];
    foreach ($urls as $name => $url) {
        $ch = curl_init();
        curl_setopt_array($ch, [CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 5, CURLOPT_CONNECTTIMEOUT => 5, ]);
        curl_multi_add_handle($mh, $ch);
        $chs[$name] = $ch;
    }
    do {
        curl_multi_exec($mh, $start);
    } while ($start);
    $last = [];
    $ask = [];
    $bid = [];
    $vol = [];
    foreach ($chs as $name => $ch) {
        $res = json_decode(curl_multi_getcontent($ch));
        switch ($name) {
            case 'ZA': // Zaif
            case 'ZAB': // Zaif
                $last[$name] = $res->last;
                $ask[$name] = $res->ask;
                $bid[$name] = $res->bid;
                $vol[$name] = $res->volume;
            break;
            case 'PX': // Poloniex
            $res = $res->BTC_XEM;
                $last[$name] = $res->last;
                $ask[$name] = $res->lowestAsk;
                $bid[$name] = $res->highestBid;
                $vol[$name] = $res->baseVolume;
            break;
            case 'CC': // CoinCheck
                $BTC_JPY = $res->last;
            break;
        }
    }
   //sprintf('%.3f',$last['CP'])
    $msg_BTC = 
    'XEM/BTC'.PHP_EOL.
    '-----------+-----------+-----------+-----------+-----------'.PHP_EOL.
    ' name      | last      | ask       | bid       | vol       '.PHP_EOL.
    '-----------+-----------+-----------+-----------+-----------'.PHP_EOL.
    ' Poloniex  | '.
    sprintf('%.8f', $last['PX']).'| '.
    sprintf('%.8f', $ask['PX']).'| '.
    sprintf('%.8f', $bid['PX']).'| '.
    sprintf('%-10s', number_format($vol['PX']/$last['PX'])).
    PHP_EOL.
    ' Zaif      | '.
    sprintf('%.8f', $last['ZAB']).'| '.
    sprintf('%.8f', $ask['ZAB']).'| '.
    sprintf('%.8f', $bid['ZAB']).'| '.
    sprintf('%-10s', number_format($vol['ZAB'])).
    PHP_EOL;
    $msg_JPY =
    'XEM/JPY BTC = '.sprintf('%-10s', number_format($BTC_JPY)).PHP_EOL.
    '-----------+-----------+-----------+-----------+-----------'.PHP_EOL.
    ' name      | last      | ask       | bid       | vol       '.PHP_EOL.
    '-----------+-----------+-----------+-----------+-----------'.PHP_EOL.
    ' Poloniex  | '.
    sprintf('%-10s', number_format($last['PX']*$BTC_JPY)).'| '.
    sprintf('%-10s', number_format($ask['PX']*$BTC_JPY)).'| '.
    sprintf('%-10s', number_format($bid['PX']*$BTC_JPY)).'| '.
    sprintf('%-10s', number_format($vol['PX']/$last['PX'])).
    PHP_EOL.
    '-----------+-----------+-----------+-----------+-----------'.PHP_EOL.
    ' Zaif      | '.
    sprintf('%-10s', number_format($last['ZA'])).'| '.
    sprintf('%-10s', number_format($ask['ZA'])).'| '.
    sprintf('%-10s', number_format($bid['ZA'])).'| '.
    sprintf('%-10s', number_format($vol['ZA'])).
    PHP_EOL;

    return	PHP_EOL . '```js' . PHP_EOL . $msg_BTC . '```'.
    		PHP_EOL . '```js' . PHP_EOL . $msg_JPY . '```';
}, 
['description' => 'XEM価格を表示します。', ]
);

