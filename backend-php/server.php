<?php

require_once __DIR__.'/vendor/autoload.php';

use Psr\Http\Message\ServerRequestInterface;
use React\Http;
use React\Socket;

$getopt = new \GetOpt\GetOpt([
    ['p', 'port', \GetOpt\GetOpt::OPTIONAL_ARGUMENT, 'Server port', 8080],
]);
$getopt->process();

$store = [];
$currentId = 0;
$port = $getopt->getOption('port');

/** @var \Silex\Application $app */
$app = require __DIR__ . '/app.php';
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
$socket = new Socket\Server($port, $loop);
$server->listen($socket);

echo "Server running at http://127.0.0.1:$port\n";

$loop->run();
