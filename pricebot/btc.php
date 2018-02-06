<?php

$cmd_client->registerCommand('btc', function ($message) {
    $urls = [];
    $urls['BFFX'] = 'https://api.bitflyer.jp/v1/ticker?product_code=FX_BTC_JPY'; // bitFlyerFX
    $urls['BF'] = 'https://api.bitflyer.jp/v1/ticker?product_code=BTC_JPY'; // bitFlyer
    $urls['BFF1'] = 'https://api.bitflyer.jp/v1/ticker?product_code=BTCJPY_MAT1WK'; // bitFlyer 先物1
    $urls['BFF2'] = 'https://api.bitflyer.jp/v1/ticker?product_code=BTCJPY_MAT2WK'; // bitFlyer 先物2
    $urls['ZAIF'] = 'https://api.zaif.jp/api/1/ticker/btc_jpy'; //zaif
    $urls['CC'] = 'https://coincheck.com/api/ticker'; // CoinCheck
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
            case 'BFFX': // bitFlyerFX
                $last[$name] = $res->ltp;
                $ask[$name] = $res->best_ask;
                $bid[$name] = $res->best_bid;
                $vol[$name] = $res->volume_by_product;
            break;
            case 'BF': // bitFlyer
                $last[$name] = $res->ltp;
                $ask[$name] = $res->best_ask;
                $ask[$name] = $res->best_ask;
                $bid[$name] = $res->best_bid;
                $vol[$name] = $res->volume_by_product;
            break;
            case 'BFF1': // bitFlyer先物1
                $last[$name] = $res->ltp;
                $ask[$name] = $res->best_ask;
                $ask[$name] = $res->best_ask;
                $bid[$name] = $res->best_bid;
                $vol[$name] = $res->volume_by_product;
            break;
            case 'BFF2': // bitFlyer先物2
                $last[$name] = $res->ltp;
                $ask[$name] = $res->best_ask;
                $ask[$name] = $res->best_ask;
                $bid[$name] = $res->best_bid;
                $vol[$name] = $res->volume_by_product;
            break;
            case 'ZAIF': //zaif
                $last[$name] = $res->last;
                $ask[$name] = $res->ask;
                $bid[$name] = $res->bid;
                $vol[$name] = $res->volume;
            break;
            case 'CC': // CoinCheck
                $last[$name] = $res->last;
                $ask[$name] = $res->ask;
                $bid[$name] = $res->bid;
                $vol[$name] = $res->volume;
            break;
        }
    }
   
    $msg = 
    '-----------+-----------+-----------+-----------+-----------'.PHP_EOL.
    ' name      | last      | ask       | bid       | vol       '.PHP_EOL.
    '-----------+-----------+-----------+-----------+-----------'.PHP_EOL.
    ' ZAIF      | '.
    sprintf('%-10s', number_format($last['ZAIF'])).'| '.
    sprintf('%-10s', number_format($ask['ZAIF'])).'| '.
    sprintf('%-10s', number_format($bid['ZAIF'])).'| '.
    sprintf('%-10s', number_format($vol['ZAIF'])).
    PHP_EOL.
    ' CoinCheck | '.
    sprintf('%-10s', number_format($last['CC'])).'| '.
    sprintf('%-10s', number_format($ask['CC'])).'| '.
    sprintf('%-10s', number_format($bid['CC'])).'| '.
    sprintf('%-10s', number_format($vol['CC'])).
    PHP_EOL.
    ' bitFlyer  | '.
    sprintf('%-10s', number_format($last['BF'])).'| '.
    sprintf('%-10s', number_format($ask['BF'])).'| '.
    sprintf('%-10s', number_format($bid['BF'])).'| '.
    sprintf('%-10s', number_format($vol['BF'])).
    PHP_EOL.
    ' bitFlyerFX| '.
    sprintf('%-10s', number_format($last['BFFX'])).'| '.
    sprintf('%-10s', number_format($ask['BFFX'])).'| '.
    sprintf('%-10s', number_format($bid['BFFX'])).'| '.
    sprintf('%-10s', number_format($vol['BFFX'])).
    PHP_EOL.
    ' BFFurture1| '.
    sprintf('%-10s', number_format($last['BFF1'])).'| '.
    sprintf('%-10s', number_format($ask['BFF1'])).'| '.
    sprintf('%-10s', number_format($bid['BFF1'])).'| '.
    sprintf('%-10s', number_format($vol['BFF1'])).
    PHP_EOL.
    ' BFFurture2| '.
    sprintf('%-10s', number_format($last['BFF2'])).'| '.
    sprintf('%-10s', number_format($ask['BFF2'])).'| '.
    sprintf('%-10s', number_format($bid['BFF2'])).'| '.
    sprintf('%-10s', number_format($vol['BFF2'])).
    PHP_EOL;

    return PHP_EOL . '```js' . PHP_EOL . $msg . '```' . PHP_EOL;
}, 
['description' => 'BTC価格を表示します。', ]
);
