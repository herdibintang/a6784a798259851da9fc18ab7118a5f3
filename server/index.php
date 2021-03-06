<?php

require_once './vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Phroute\Phroute\Exception\HttpMethodNotAllowedException;
use Phroute\Phroute\Exception\HttpRouteNotFoundException;
use Phroute\Phroute\RouteCollector;
use Illuminate\Database\Capsule\Manager as Capsule;
use \Firebase\JWT\JWT;


$capsule = new Capsule;

$capsule->addConnection(
    [
        'driver'    => 'pgsql',
        'host'      => $_ENV['DB_HOST'],
        'database'  => 'email_sender',
        'username'  => 'root',
        'password'  => 'root',
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => '',
    ]
);
$capsule->setAsGlobal();

$router = new RouteCollector();

$router->post(
    '/user',
    function () {
        $requestBody = json_decode(file_get_contents('php://input'));

        $hash = password_hash($requestBody->password, PASSWORD_BCRYPT);

        Capsule::table('users')->insert(
            [
                'email' => $requestBody->email,
                'password' => $hash,
            ]
        );

        echo json_encode(
            [
                'message' => 'User created'
            ]
        );

        return;
    }
);

$router->post(
    '/user/login',
    function () {
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

        $jwt = JWT::encode([], $_ENV['JWT_KEY']);

        echo json_encode(
            [
                'message' => 'Token created',
                'token' => $jwt
            ]
        );

        return;
    }
);


$connection = new AMQPStreamConnection($_ENV['QUEUE_HOST'], 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('hello', false, false, false, false);

$router->post(
    '/email/send',
    function () use ($channel, $connection) {
        $headers = getallheaders();

        if (empty($headers['Authorization'])) {
            http_response_code(401);
            return;
        }

        $token = '';
        if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
            $token = $matches[1];
        }

        try {
            $decoded = JWT::decode($token, $_ENV['JWT_KEY'], array('HS256'));
        } catch (\Exception $e) {
            http_response_code(401);
            return;
        }

        $requestBody = file_get_contents('php://input');

        $msg = new AMQPMessage($requestBody);

        $channel->basic_publish($msg, '', 'hello');

        $channel->close();
        $connection->close();

        echo json_encode(
            [
                "message" => "Email request added to queue"
            ]
        );

        return;
    }
);

$dispatcher = new Phroute\Phroute\Dispatcher($router->getData());

try {
    $response = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
} catch (HttpRouteNotFoundException $e) {
    http_response_code(404);
    echo $e->getMessage();
} catch (HttpMethodNotAllowedException $e) {
    http_response_code(405);
    echo $e->getMessage();
}
