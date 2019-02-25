<?php
require_once  '../../../vendor/autoload.php';
require 'MemcachedTelnet.php';
ini_set('display_errors', 'On'); // сообщения с ошибками будут показываться
error_reporting(E_ALL); // E_ALL - отображаем ВСЕ ошибки

$client = MemcachedTelnet::factory();
$dsn = 'localhost:11211';
$client->connect($dsn, "", null, null, "\r\n");

$command = 'get key';
$resp = $client->execute($command);
var_dump($resp);