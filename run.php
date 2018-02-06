<?php
setlocale(LC_NUMERIC, 'ja_JP.utf8');
use Discord\DiscordCommandClient;

include __DIR__ . '/vendor/autoload.php';
$config = json_decode(file_get_contents(__DIR__ . '/config.json'));


$cmd_client = new DiscordCommandClient([
	'token' => $config->token,
	'prefix' => $config->prefix, 
]);

include __DIR__.'/pricebot/btc.php';
include __DIR__.'/pricebot/bch.php';

include __DIR__.'/pricebot/eth.php';
include __DIR__.'/pricebot/ecob.php';
include __DIR__.'/pricebot/xem.php';
include __DIR__.'/pricebot/xrp.php';




$cmd_client->on('ready', function ($cmd_client) {
    echo "Bot is ready.", PHP_EOL;
  
    $cmd_client->on('message', function ($message) {
    });
});

$cmd_client->run();
