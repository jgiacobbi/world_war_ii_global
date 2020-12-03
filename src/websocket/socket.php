<?php

use Axis\AxisServer;
use Axis\ConnectionRegistry;
use Axis\Globals;
use Axis\Log;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

require dirname(__DIR__, 2) . '/vendor/autoload.php';

//php doesn't have static initializers
Globals::Init();
ConnectionRegistry::Init();

Log::info("Project root is " . Globals::$root);
Log::info("Logging to " . Globals::$logs);

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            AxisServer::buildServer(Globals::$files, Globals::$data)
        )
    ),
    8080
);

$server->run();
