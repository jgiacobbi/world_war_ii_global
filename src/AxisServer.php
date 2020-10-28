<?php

namespace Axis;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class AxisServer implements MessageComponentInterface {
    protected $clients;
    protected $logger;

    public function __construct() {
        $clients = new \SplObjectStorage;
        $logger = new Logger('axis',
            [
                new RotatingFileHandler("/var/log/axis/all.log", 10),
                new RotatingFileHandler("/var/log/axis/error.log", 10, Logger::ERROR)
            ]
        );
    }

    public function onOpen(ConnectionInterface $conn) {
        $logger->info("New connection! ({$conn->resourceId})");
        $this->clients->attach($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $logger->debug("Message from {$from->resourceId} : $msg");
        $this->handleMessage($from, $msg);
    }

    public function onClose(ConnectionInterface $conn) {
        $logger->info("Connection {$conn->resourceId} has disconnected");
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        $logger->critical("An error has occurred: {$e->getMessage()}");

        $conn->close();
    }

    public function handleMessage(ConnectionInterface $conn, $msg){
        $message = json_decode($msg, true);

        if ($message == null) {
            $logger->error("Invalid Json: $msg");
            $conn->send("Invalid Json");
            return;
        }
    }
}