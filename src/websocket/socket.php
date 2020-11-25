<?php

use Axis\AxisServer;
use Axis\ConnectionRegistry;
use Axis\Globals;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

require dirname(__DIR__, 2) . '/vendor/autoload.php';

//php doesn't have static initializers
Globals::Init();
ConnectionRegistry::Init();

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new AxisServer(Globals::$logs)
        )
    ),
    8080
);

$server->run();