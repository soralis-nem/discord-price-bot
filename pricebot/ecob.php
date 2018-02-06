<?php

$cmd_client->registerCommand('ecob', function ($message) {
    $urls = [];
    $urls['CP'] = 'https://www.cryptopia.co.nz/api/GetMarket/ECOB_BTC'; // Cryptpia
    $urls['YB'] = 'https://yobit.net/api/3/ticker/ecob_btc'; // Yobit
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
            case 'CP': // Cryptpia
            $res = $res->Data;
                $last[$name] = $res->LastPrice;
                $ask[$name] = $res->AskPrice;
                $bid[$name] = $res->BidPrice;
                $vol[$name] = $res->Volume;
            break;
            case 'YB': // Yobit
            $res = $res->ecob_btc;
                $last[$name] = $res->last;
                $ask[$name] = $res->sell;
                $bid[$name] = $res->buy;
                $vol[$name] = $res->vol_cur;
            break;
            case 'CC': // CoinCheck
                $BTC_JPY = $res->last;
            break;
        }
    }
   //sprintf('%.3f',$last['CP'])
    $msg_BTC = 
    'ECOB/BTC'.PHP_EOL.
    '-----------+-----------+-----------+-----------+-----------'.PHP_EOL.
    ' name      | last      | ask       | bid       | vol       '.PHP_EOL.
    '-----------+-----------+-----------+-----------+-----------'.PHP_EOL.
    ' Cryptpia  | '.
    sprintf('%.8f', $last['CP']).'| '.
    sprintf('%.8f', $ask['CP']).'| '.
    sprintf('%.8f', $bid['CP']).'| '.
    sprintf('%-10s', number_format($vol['CP'])).
    PHP_EOL.
    ' Yobit     | '.
    sprintf('%.8f', $last['YB']).'| '.
    sprintf('%.8f', $ask['YB']).'| '.
    sprintf('%.8f', $bid['YB']).'| '.
    sprintf('%-10s', number_format($vol['YB'])).
    PHP_EOL;
    $msg_JPY =
    'ECOB/JPY BTC = '.sprintf('%-10s', number_format($BTC_JPY)).PHP_EOL.
    '-----------+-----------+-----------+-----------+-----------'.PHP_EOL.
    ' name      | last      | ask       | bid       | vol       '.PHP_EOL.
    '-----------+-----------+-----------+-----------+-----------'.PHP_EOL.
    ' Cryptpia  | '.
    sprintf('%-10s', number_format($last['CP']*$BTC_JPY)).'| '.
    sprintf('%-10s', number_format($ask['CP']*$BTC_JPY)).'| '.
    sprintf('%-10s', number_format($bid['CP']*$BTC_JPY)).'| '.
    sprintf('%-10s', number_format($vol['CP'])).
    PHP_EOL.
    ' Yobit     | '.
    sprintf('%-10s', number_format($last['YB']*$BTC_JPY)).'| '.
    sprintf('%-10s', number_format($ask['YB']*$BTC_JPY)).'| '.
    sprintf('%-10s', number_format($bid['YB']*$BTC_JPY)).'| '.
    sprintf('%-10s', number_format($vol['YB'])).
    PHP_EOL;

    return	PHP_EOL . '```js' . PHP_EOL . $msg_BTC . '```'.
    		PHP_EOL . '```js' . PHP_EOL . $msg_JPY . '```';
}, 
['description' => 'BTC価格を表示します。', ]
);

