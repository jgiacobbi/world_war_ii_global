<?php

namespace Axis;

use Monolog\Logger;
use Ratchet\ConnectionInterface;

class MessageHandler {
    private $logger;
    protected $auth;
    protected $lobbies;

    public function __construct(Logger $logger) {
        $this->logger = $logger;

        $this->auth = new Auth($this->logger);
        $this->lobbies = new LobbyContainer();
    }

    public function handle(ConnectionInterface $conn, array $message) {
        if (!isset($message["method"]) || empty($message["method"])) {
            throw new \Exception("No method specified");
        }

        return $this->{$message["method"]}($message["payload"] ?? []);

        /*$payload = $message["payload"];

        switch($message["method"]) {
            case "createLobby":
                return $this->lobbies->add($payload);
            case "renameLobby":
                return $this->lobbies->rename($payload);
            case "joinLobby":
                $payload["connectionId"] = $conn->resourceId;
                return $this->lobbies->addUser($payload);
            case "leaveLobby":
                $payload["connectionId"] = $conn->resourceId;
                return $this->lobbies->removeUser($payload);
            case "lobbyMessage":
                $this->lobbies->message($payload);
                break;
            case "login":
                $response = $this->auth->login($payload);

                ConnectionRegistry::SetName($conn->resourceId, $response["name"]);

                return $response;
            default:
                throw new \Exception("Unknown method: {$message["method"]}");
        }*/
    }

    private function loadPolygons() {
        return json_decode(file_get_contents(dirname(__DIR__) . "/data/polygons.json"), true);
    }

    private function loadPlacements() {
        return json_decode(file_get_contents(dirname(__DIR__) . "/data/placements.json"), true);
    }

    public function __call($name, $arguments) {
        // Returning this error to the client through the top level exception handler is preferable to
        // PHP Fatal error:  Uncaught Error: Call to undefined method stdClass::lolwut() in Command line code:1

        throw new \Exception("Unknown method: $name");
    }
}
