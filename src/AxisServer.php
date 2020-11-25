<?php

namespace Axis;

use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class AxisServer implements MessageComponentInterface {
    protected $logRoot;
    protected $logger;
    protected $handler;

    public function __construct(string $logRoot) {
        $this->logRoot = $logRoot;
        @mkdir($this->logRoot, 0777, true);

        $this->logger = new Logger('axis',
            [
                new StreamHandler("php://stdout"),
                new RotatingFileHandler("{$this->logRoot}/all.log", 10),
                new RotatingFileHandler(
                    "{$this->logRoot}/error.log", 10, Logger::ERROR)
            ]
        );

        $this->handler = new MessageHandler($this->logger);
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

        if (is_null($message)) {
            $this->logger->error("Invalid Json: $msg");
            $conn->send(json_encode(["error" => "Invalid Json: $msg"]));
            return;
        }

        $response = [];

        try {
            $retval = $this->handler->handle($conn, $message);

            if (is_array($retval)) {
                $response = $retval;
            } else {
                $response["result"] = $retval;
            }

            if (isset($message["id"])) {
                $response = [
                    "id" => $message["id"],
                    "body" => $response
                ];
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $response = ["error" => $e->getMessage()];
            if (isset($message["id"])) {
                $response["id"] = $message["id"];
            }
        } finally {
            $conn->send(json_encode($response));
        }
    }
}
