<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Illuminate\Database\Capsule\Manager as Capsule;

$connection = new AMQPStreamConnection($_ENV['QUEUE_HOST'], 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('hello', false, false, false, false);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) {
  echo ' [x] Received ', $msg->body, "\n";

  $requestBody = json_decode($msg->body);
  
  // Create the Transport
  $transport = (new Swift_SmtpTransport($_ENV['MAIL_HOST'], $_ENV['MAIL_PORT']))
    ->setUsername($_ENV['MAIL_USERNAME'])
    ->setPassword($_ENV['MAIL_PASSWORD'])
  ;

  // Create the Mailer using your created Transport
  $mailer = new Swift_Mailer($transport);

  // Create a message
  $message = (new Swift_Message($requestBody->subject))
    ->setFrom($requestBody->from)
    ->setTo($requestBody->to)
    ->setBody($requestBody->body)
    ;

  // Send the message
  $result = $mailer->send($message);

  if($result){
    $capsule = new Capsule;

    $capsule->addConnection([
        'driver'    => 'pgsql',
        'host'      => $_ENV['DB_HOST'],
        'database'  => $_ENV['DB_DATABASE'],
        'username'  => $_ENV['DB_USERNAME'],
        'password'  => $_ENV['DB_PASSWORD'],
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => '',
    ]);
    $capsule->setAsGlobal();

    Capsule::table('emails')->insert([
      'from' => 'from',
      'to' => 'to',
      'subject' => 'subject',
      'body' => 'body'
    ]);
  }

  echo " [x] Done\n";
};

$channel->basic_consume('hello', '', false, true, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();
?>