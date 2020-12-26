<?php

namespace Axis;

use Axis\Games\DiskStorage;
use Axis\Games\GameRunner;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class AxisServer implements MessageComponentInterface
{
    protected MessageHandler $handler;

    public function __construct(MessageHandler $handler)
    {
        $this->handler = $handler;
    }

    public static function buildServer(string $files, string $data): self
    {
        $gameStorage = new DiskStorage($files, $data);
        $gameRunner = new GameRunner($gameStorage);
        $auth = new Auth();
        $messageHandler = new MessageHandler($auth, $gameRunner);

        return new self($messageHandler);
    }

    public function onOpen(ConnectionInterface $conn)
    {
        Log::info("New connection! ({$conn->resourceId})");
        ConnectionRegistry::Add($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        Log::debug("Message from {$from->resourceId} : $msg");
        $this->handleMessage($from, $msg);
    }

    public function onClose(ConnectionInterface $conn)
    {
        Log::info("Connection {$conn->resourceId} has disconnected");
        ConnectionRegistry::Remove($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        Log::critical("An error has occurred: {$e->getMessage()}");

        $conn->close();
    }

    public function handleMessage(ConnectionInterface $conn, $msg)
    {
        $message = json_decode($msg, true);

        if (is_null($message)) {
            Log::error("Invalid Json: $msg");
            $conn->send(json_encode(['error' => "Invalid Json: $msg"]));

            return;
        }

        $response = [];

        try {
            $retval = $this->handler->handle($conn, $message);

            if (is_array($retval)) {
                $response = $retval;
            } else {
                $response['result'] = $retval;
            }

            if (isset($message['id'])) {
                $response = [
                    'id' => $message['id'],
                    'body' => $response,
                ];
            }
        } catch (\Exception $e) {
            Log::error(
                $e->getMessage(),
                ConnectionRegistry::GetRawContext($conn->resourceId)
            );

            $response = ['error' => $e->getMessage()];
            if (isset($message['id'])) {
                $response['id'] = $message['id'];
            }
        } finally {
            $conn->send(json_encode($response));
        }
    }
}
