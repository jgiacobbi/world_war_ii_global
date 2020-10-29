<?php

use Axis\AxisServer;
use Axis\ConnectionRegistry;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

require dirname(__DIR__) . '/vendor/autoload.php';

//php doesn't have static initializers
ConnectionRegistry::Init();

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new AxisServer()
        )
    ),
    8080
);

$server->run();