<?php

require_once __DIR__.'/vendor/autoload.php';

use JsonSchema\Constraints\Constraint;
use Psr\Http\Message\ServerRequestInterface;
use React\Http;
use React\Socket;
use Silex\Application;
use Symfony\Component\HttpFoundation;

$store = [];
$currentId = 0;

$app = new Application();

$app->register(new JDesrosiers\Silex\Provider\CorsServiceProvider());

// Endpoint: Get By ID
$app->get('/{id}', function ($id) use (&$store) {
    if (!\array_key_exists((int) $id, $store)) { return new HttpFoundation\JsonResponse(null, 404); }
    return new HttpFoundation\JsonResponse($store[(int) $id]);
})->assert('id', '\d+');

// Endpoint: Get List
$app->get('/', function() use (&$store) { return new HttpFoundation\JsonResponse(array_values($store)); });

// Endpoint: Store
$app->post('/', function (HttpFoundation\Request $request) use (&$currentId, &$store) {
    $content = \json_decode($request->getContent());

    $validator = new \JsonSchema\Validator();
    $validator->validate(
        $content,
        (object)['$ref' => 'file://' . __DIR__ . '/../schema/product.json'],
        Constraint::CHECK_MODE_COERCE_TYPES
    );

    if (!$validator->isValid()) {
        return new HttpFoundation\JsonResponse($validator->getErrors(), 400);
    }

    $data = (array) $content;
    $data['id'] = ++$currentId;
    $store[$currentId] = $data;

    return new HttpFoundation\JsonResponse($store[$currentId], 202);
});

$app->after(function (HttpFoundation\Request $request, HttpFoundation\Response $response) {
    $response->headers->set('Access-Control-Allow-Origin', '*');
    $response->headers->set('Access-Control-Allow-Headers', 'X-CSRF-Token, X-Requested-With, Accept, Accept-Version, Content-Length, Content-MD5, Content-Type, Date, X-Api-Version, Origin');
    $response->headers->set('Access-Control-Allow-Methods', 'GET, PUT, POST, DELETE, OPTIONS');
});

$app->boot();

$server = new Http\Server(function (ServerRequestInterface $request) use ($app) {
    return new \React\Promise\Promise(function ($resolve) use ($app, $request) {
        $body = '';
        $request->getBody()
            ->on('data', function ($data) use (&$body) {$body .= $data; })
            ->on('error', function (\Exception $exception) use ($resolve, &$contentLength) {
                $resolve(new Http\Response(400, ['Content-Type' => 'text/plain'], "An error occured while reading at length: " . $contentLength));
            })
            ->on('end', function () use ($app, &$body, $request, $resolve) {
                $sResponse = $app->handle(\Symfony\Component\HttpFoundation\Request::create(
                    $request->getRequestTarget(),
                    $request->getMethod(),
                    $request->getAttributes(),
                    $request->getCookieParams(),
                    $request->getUploadedFiles(),
                    $request->getServerParams(),
                    $body
                ));
                $resolve(new Http\Response(
                    $sResponse->getStatusCode(),
                    $sResponse->headers->all(),
                    $sResponse->getContent()
                ));
            });
    });
});

// Event Loop
$loop = \React\EventLoop\Factory::create();
$socket = new Socket\Server(8080, $loop);
$server->listen($socket);

echo "Server running at http://127.0.0.1:8080\n";

$loop->run();
