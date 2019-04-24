<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

require_once __DIR__ . '/vendor/autoload.php';

$data = implode(' ', array_slice($argv, 1));
if (empty($data)) {
  $data = "Hello World!";
}

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

//Third parameter is set to true because we want our messages to be persistent
$channel->queue_declare('helloQueue', false, true);

$msg = new AMQPMessage($data, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);

$channel->basic_publish($msg, '', 'helloQueue');

echo ' [x] Sent ', $data, "\n";
$channel->close();
$connection->close();

