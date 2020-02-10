#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("producer.ini","database");
if (isset($argv[1]) && isset($argv[2]) && isset($arv[3]))
{
  $usr = $argv[1];
  $pas = $argv[2];
  $msg = $argv[3];
}
else
{
  $usr = "test";
  $pas = "test";
  $msg = "test message";
}

$request = array();
$request['type'] = "Login";
$request['username'] = $usr;
$request['password'] = $pas;
$request['message'] = $msg;
$response = $client->send_request($request);
//$response = $client->publish($request);

echo "client received response: ".PHP_EOL;
print_r($response);
echo "\n\n";

echo $argv[0]." END".PHP_EOL;

