<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();
$channel->queue_declare('helloQueue');

$msg = new AMQPMessage('Hello World2!');
$channel->basic_publish($msg, '', 'helloQueue');

echo "[x] Sent 'Hello world'<br>";

$channel->close();
$connection->close();
