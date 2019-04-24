<?php
namespace RabbitMQ\Tutorial;
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Worker
{

  /**
   * @var $connection AMQPStreamConnection
   */
  public $connection;

  /**
   * @var $channel AMQPChannel
   */
  public $channel;

  /**
   * Worker constructor.
   */
  public function __construct()
  {
    $this->connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
    $this->channel = $this->connection->channel();
    $this->channel->queue_declare('helloQueue', false, true);
  }

  public function callback(AMQPMessage $msg)
  {
    echo ' [x] Received ', $msg->body, "\n";
    sleep(substr_count($msg->body, '.'));
    echo " [x] Done\n";

    /**
     * @var $channel AMQPChannel
     */
    $channel = $msg->delivery_info['channel'];
    $channel->basic_ack($msg->delivery_info['delivery_tag']);
  }

  public function listen() {
    echo " [*] Waiting for messages. To exit press CTRL+C\n";

    $this->channel->basic_consume('helloQueue', '', false, false, false, false, [$this, 'callback']);

    while (count($this->channel->callbacks)) {
      $this->channel->wait();
    }

    $this->channel->close();
    $this->connection->close();

  }

}

$worker = new Worker();
$worker->listen();
