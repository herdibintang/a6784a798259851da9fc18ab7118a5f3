<?php

require_once './vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


// Create the Transport
$transport = (new Swift_SmtpTransport($_ENV['MAIL_HOST'], $_ENV['MAIL_PORT']))
  ->setUsername($_ENV['MAIL_USERNAME'])
  ->setPassword($_ENV['MAIL_PASSWORD'])
;

// Create the Mailer using your created Transport
$mailer = new Swift_Mailer($transport);

// Create a message
$message = (new Swift_Message('Wonderful Subject'))
  ->setFrom('john@doe.com')
  ->setTo(['receiver@domain.org', 'other@domain.org' => 'A name'])
  ->setBody('Here is the message itself')
  ;

// Send the message
$result = $mailer->send($message);

echo 'done';