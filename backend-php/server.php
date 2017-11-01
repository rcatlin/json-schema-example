<?php

use Psr\Http\Message\ServerRequestInterface;
use React\Http;
use React\Socket;

require_once __DIR__.'/vendor/autoload.php';

$dereferencer = League\JsonReference\Dereferencer::draft4();
$schema = $dereferencer->dereference('file://'.__DIR__.'/../schema/product.json');
$store = [];
$currentId = 0;

// Validates Data
$validate = function ($data) use ($schema) {
    $validator = new League\JsonGuard\Validator($data, $schema);
    if ($validator->fails()) {
        return $validator->errors();
    }
};

// Handle GET Request
$handleGet = function (ServerRequestInterface $request) use (&$store, $validate) {
    return new \React\Promise\Promise(function ($resolve, $reject) use ($request, &$store, $validate) {
        // Get Route ID
        $matches = [];
        if (!preg_match('/^\/(\d+)$/', $request->getRequestTarget(), $matches)) {
            return $resolve(new Http\Response(404));
        }
        $id = (string) $matches[1];

        // Check if Product Exists
        if (!array_key_exists($id, $store)) {
            return $resolve(new Http\Response(404, ['Content-Type' => 'text/plain'], ' Product not found'));
        }

        $product = $store[$id];

        // Validate Response Data
        $errors = $validate($product);
        if ($errors) {
            return $resolve(new Http\Response(500, ['Content-Type' => 'text/plain'], 'Internal data fails validation'));
        }

        // Return Product
        $resolve(new Http\Response(200, ['Content-Type' => 'application/json'], \json_encode($product)));
    });
};

// Handle POST Request
$handlePost = function (ServerRequestInterface $request) use (&$currentId, $schema, &$store, $validate) {
    return new \React\Promise\Promise(function ($resolve, $reject) use (&$currentId, $request, $validate, &$store) {
        $body = '';
        $request->getBody()
            ->on('data', function ($data) use (&$body) { $body .= $data; })
            ->on('error', function (\Exception $exception) use ($resolve, &$contentLength) {
                $resolve(new Http\Response(400, ['Content-Type' => 'text/plain'], "An error occured while reading at length: " . $contentLength));
            })
            ->on('end', function () use ($resolve, &$currentId, &$body, $validate, &$store){
                $data = \json_decode($body);

                // Validate Request Data
                $errors = $validate($data);
                if ($errors) {
                    return $resolve(new Http\Response(400, ['Content-Type' => 'application/json'], \json_encode(
                        array_map(
                            function (League\JsonGuard\ValidationError $error) {
                                return $error->toArray();
                            },
                            $errors
                        )
                    )));
                }

                // Generate Id
                $data->id = ++$currentId;
                $store[(string) $currentId] = $data;

                // Validate Response Data
                $errors = $validate($store[$currentId]);
                if ($errors) {
                    return $resolve(new Http\Response(500, ['Content-Type' => 'text/plain'], 'Internal data fails validation'));
                }

                // Return Product
                $resolve(new Http\Response(202, ['Content-Type' => 'application/json'], \json_encode($store[$currentId])));
            });
    });
};

$server = new Http\Server(function (ServerRequestInterface $request) use ($handleGet, $handlePost) {
    switch ($request->getMethod()) {
        case 'GET':
            $response =  $handleGet($request);
            break;
        case 'POST':
            $response = $handlePost($request);
            break;
        default:
            $response = new Http\Response(405);
    }

    return $response;
});

// Event Loop
$loop = \React\EventLoop\Factory::create();

$socket = new Socket\Server(8080, $loop);
$server->listen($socket);

echo "Server running at http://127.0.0.1:8080\n";

$loop->run();
