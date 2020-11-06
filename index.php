<?php

require_once './vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Phroute\Phroute\Exception\HttpMethodNotAllowedException;
use Phroute\Phroute\Exception\HttpRouteNotFoundException;
use Phroute\Phroute\RouteCollector;
use Illuminate\Database\Capsule\Manager as Capsule;
use \Firebase\JWT\JWT;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

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

$router = new RouteCollector();

$router->post('/user', function () {
  $requestBody = json_decode(file_get_contents('php://input'));

  $hash = password_hash($requestBody->password, PASSWORD_BCRYPT);

  Capsule::table('users')->insert([
    'email' => $requestBody->email,
    'password' => $hash,
  ]);
});

$router->post('/user/login', function () {
  $requestBody = json_decode(file_get_contents('php://input'));

  $res = Capsule::table('users')
    ->where('email', $requestBody->email)
    ->first();

  if (!$res) {
    http_response_code(404);
    return;
  }

  if (!password_verify($requestBody->password, $res->password)) {
    http_response_code(400);
    return;
  }

  $jwt = JWT::encode([], 'test');

  echo json_encode([
    'token' => $jwt
  ]);

  return;
});

$dispatcher = new Phroute\Phroute\Dispatcher($router->getData());

try {
  $response = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
} catch (HttpRouteNotFoundException $e) {
  echo $e->getMessage();
} catch (HttpMethodNotAllowedException $e) {
  echo $e->getMessage();
}
// Print out the value returned from the dispatched function
// echo $response;


// $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
// $channel = $connection->channel();

// $channel->queue_declare('hello', false, false, false, false);


// $requestBody = file_get_contents('php://input');

// $msg = new AMQPMessage($requestBody);

// $channel->basic_publish($msg, '', 'hello');


// $channel->close();
// $connection->close();