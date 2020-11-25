<?php

namespace Axis;

use Monolog\Logger;
use Ratchet\ConnectionInterface;

class MessageHandler {
    private $logger;
    protected $auth;
    protected $games;

    public function __construct(Logger $logger) {
        $this->logger = $logger;

        $this->auth = new Auth($this->logger);
        $this->games = new Games($this->logger, Globals::$files, Globals::$data);
    }

    public function handle(ConnectionInterface $conn, array $message) {
        if (!isset($message["method"]) || empty($message["method"])) {
            throw new \Exception("No method specified");
        }

        return $this->{$message["method"]}($message["payload"] ?? [], $conn);
    }

    public function sanitize(string $name) : string {
        return preg_replace("/[^a-zA-Z0-9\-]/", "", $name);
    }

    private function loadPolygons() {
        return json_decode(file_get_contents(Globals::$data . "/polygons.json"), true);
    }

    private function loadPlacements($payload, ConnectionInterface $conn) {
        $game = ConnectionRegistry::GetGameById($conn->resourceId);

        if (is_null($game)) {
            throw new \Exception("Cannot load a map without joining a game");
        }

        return $this->games->getPlacements($game);
    }

    private function login($payload, ConnectionInterface $conn) {
        $response = $this->auth->login($payload);

        ConnectionRegistry::SetName($conn->resourceId, $response["name"]);

        return $response;
    }

    private function listGames() {
        return $this->games->list();
    }

    private function joinGame($payload, ConnectionInterface $conn) {
        if (!isset($payload["name"])) {
            throw new \Exception("Can't join game without a name");
        }

        $name = $this->sanitize($payload["name"]);

        if (!$this->games->exists($name)) {
            $this->games->createGame($name);
        }

        ConnectionRegistry::SetGame($conn->resourceId, $name);

        //we have a list of player -> game, should also track game -> players
        //for broadcast game updates
        return true;
    }

    public function __call($name, $arguments) {
        // Returning this error to the client through the top level exception handler is preferable to
        // PHP Fatal error:  Uncaught Error: Call to undefined method stdClass::lolwut() in Command line code:1

        throw new \Exception("Unknown method: $name");
    }
}
