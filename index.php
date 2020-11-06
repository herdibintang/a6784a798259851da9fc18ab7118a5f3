<?php

require_once './vendor/autoload.php';
use Illuminate\Database\Capsule\Manager as Capsule;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


$requestBody = json_decode(file_get_contents('php://input'));


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

$users = Capsule::table('emails')->insert([
  'from' => 'from',
  'to' => 'to',
  'subject' => 'subject',
  'body' => 'body'
]);

// echo 'done';
echo var_dump($users);