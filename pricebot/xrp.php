<?php

$cmd_client->registerCommand('xrp', function ($message) {
    $urls = [];
    $urls['BB'] = 'https://public.bitbank.cc/xrp_jpy/ticker'; // BitBank
    $urls['PX'] = 'https://poloniex.com/public?command=returnTicker'; //Poloniex
    $urls['BS'] = 'https://www.bitstamp.net/api/v2/ticker/xrpbtc'; //BitStamp
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
            case 'BB': // BitBank
            $res = $res->data;
                $last[$name] = $res->last;
                $ask[$name] = $res->sell;
                $bid[$name] = $res->buy;
                $vol[$name] = $res->vol;
            break;
            case 'PX': // Poloniex
            $res = $res->BTC_XRP;
                $last[$name] = $res->last;
                $ask[$name] = $res->lowestAsk;
                $bid[$name] = $res->highestBid;
                $vol[$name] = $res->baseVolume;
            break;
            case 'BS': // Bitstamp
                $last[$name] = $res->last;
                $ask[$name] = $res->ask;
                $bid[$name] = $res->bid;
                $vol[$name] = $res->volume;
            break;
            case 'CC': // CoinCheck
                $BTC_JPY = $res->last;
            break;
        }
    }
   //sprintf('%.3f',$last['CP'])
    $msg_BTC = 
    'XRP/BTC'.PHP_EOL.
    '-----------+-----------+-----------+-----------+-----------'.PHP_EOL.
    ' name      | last      | ask       | bid       | vol       '.PHP_EOL.
    '-----------+-----------+-----------+-----------+-----------'.PHP_EOL.
    ' Poloniex  | '.
    sprintf('%.8f', $last['PX']).'| '.
    sprintf('%.8f', $ask['PX']).'| '.
    sprintf('%.8f', $bid['PX']).'| '.
    sprintf('%-10s', number_format($vol['PX']/$last['PX'])).
    PHP_EOL.
    ' Bitstamp  | '.
    sprintf('%.8f', $last['BS']).'| '.
    sprintf('%.8f', $ask['BS']).'| '.
    sprintf('%.8f', $bid['BS']).'| '.
    sprintf('%-10s', number_format($vol['BS'])).
    PHP_EOL;
    $msg_JPY =
    'XRP/JPY BTC = '.sprintf('%-10s', number_format($BTC_JPY)).PHP_EOL.
    '-----------+-----------+-----------+-----------+-----------'.PHP_EOL.
    ' name      | last      | ask       | bid       | vol       '.PHP_EOL.
    '-----------+-----------+-----------+-----------+-----------'.PHP_EOL.
    ' Poloniex  | '.
    sprintf('%-10s', number_format($last['PX']*$BTC_JPY)).'| '.
    sprintf('%-10s', number_format($ask['PX']*$BTC_JPY)).'| '.
    sprintf('%-10s', number_format($bid['PX']*$BTC_JPY)).'| '.
    sprintf('%-10s', number_format($vol['PX']/$last['PX'])).
    PHP_EOL.
    ' Bitstamp  | '.
    sprintf('%-10s', number_format($last['BS']*$BTC_JPY)).'| '.
    sprintf('%-10s', number_format($ask['BS']*$BTC_JPY)).'| '.
    sprintf('%-10s', number_format($bid['BS']*$BTC_JPY)).'| '.
    sprintf('%-10s', number_format($vol['BS'])).
    PHP_EOL.
    '-----------+-----------+-----------+-----------+-----------'.PHP_EOL.
    ' BitBank   | '.
    sprintf('%-10s', number_format($last['BB'])).'| '.
    sprintf('%-10s', number_format($ask['BB'])).'| '.
    sprintf('%-10s', number_format($bid['BB'])).'| '.
    sprintf('%-10s', number_format($vol['BB'])).
    PHP_EOL;

    return	PHP_EOL . '```js' . PHP_EOL . $msg_BTC . '```'.
    		PHP_EOL . '```js' . PHP_EOL . $msg_JPY . '```';
}, 
['description' => 'XRP価格を表示します。', ]
);

