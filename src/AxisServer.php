<?php

namespace Axis;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class AxisServer implements MessageComponentInterface {
    protected $logger;
    protected $users;

    public function __construct() {
        $this->logger = new Logger('axis',
            [
                new RotatingFileHandler("/var/log/axis/all.log", 10),
                new RotatingFileHandler("/var/log/axis/error.log", 10, Logger::ERROR)
            ]
        );

        $this->users = new Users($this->logger);
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->logger->info("New connection! ({$conn->resourceId})");
        ConnectionRegistry::Add($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $this->logger->debug("Message from {$from->resourceId} : $msg");
        $this->handleMessage($from, $msg);
    }

    public function onClose(ConnectionInterface $conn) {
        $this->logger->info("Connection {$conn->resourceId} has disconnected");
        ConnectionRegistry::Remove($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        $this->logger->critical("An error has occurred: {$e->getMessage()}");

        $conn->close();
    }

    public function handleMessage(ConnectionInterface $conn, $msg){
        $message = json_decode($msg, true);

        if ($message == null) {
            $this->logger->error("Invalid Json: $msg");
            $conn->send("Invalid Json");
            return;
        }
    }
}