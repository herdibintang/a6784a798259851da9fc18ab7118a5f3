<?php

require_once './vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('hello', false, false, false, false);


$requestBody = file_get_contents('php://input');

$msg = new AMQPMessage($requestBody);

$channel->basic_publish($msg, '', 'hello');


$channel->close();
$connection->close();