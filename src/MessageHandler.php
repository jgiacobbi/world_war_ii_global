<?php

namespace Axis;

use Axis\Games\StorageInterface;
use Axis\Games\GameRunner;
use Ratchet\ConnectionInterface;

class MessageHandler {
    protected Auth $auth;
    protected GameRunner $gameRunner;
    protected StorageInterface $gameStorage;

    public function __construct(Auth $auth, GameRunner $gameRunner) {
        $this->auth = $auth;
        $this->gameRunner = $gameRunner;
        $this->gameStorage = $gameRunner->storage();
    }

    public function handle(ConnectionInterface $conn, array $message) {
        if (!isset($message["method"]) || empty($message["method"])) {
            throw new \Exception("No method specified");
        }

        return $this->{$message["method"]}($message["payload"] ?? [], $conn);
    }

    public function sanitize(string $name) : string {
        return preg_replace("/[^a-zA-Z0-9\-\.]/", "", $name);
    }

    private function loadPolygons() {
        return json_decode(file_get_contents(Globals::$data . "/polygons.json"), true);
    }

    private function loadPlacements($payload, ConnectionInterface $conn) {
        $name = ConnectionRegistry::GetGameById($conn->resourceId);

        if (is_null($name)) {
            throw new \Exception("Cannot load a map without joining a game");
        }

        return $this->gameRunner->getGame($name)->getPlacements($name);
    }

    private function login($payload, ConnectionInterface $conn) {
        array_walk($payload, array($this, 'sanitize'));
        return $this->auth->login($conn->resourceId, $payload);
    }

    /**
     * This currently lists all games. We could differentiate between
     * games on disk and games in progress, but that seems marginal
     * right now.
     */
    private function listGames() {
        return $this->gameStorage->list();
    }

    private function joinGame($payload, ConnectionInterface $conn) {
        if (!isset($payload["name"])) {
            throw new \Exception("Can't join game without a game name");
        }

        $name = $this->sanitize($payload["name"]);

        $this->gameRunner->newGame($name)->addPlayer($conn->resourceId);

        ConnectionRegistry::SetGame($conn->resourceId, $name);

        return true;
    }

    private function coronate($payload, ConnectionInterface $conn) {
        if (!isset($payload["power"])) {
            throw new \Exception("No power to gain control off");
        }

        $gameName = ConnectionRegistry::GetGameById($conn->resourceId);
        $power = $this->sanitize($payload["power"]);

        $this->gameRunner->getGame($gameName)->coronate($conn->resourceId, $power);

        return true;
    }

    private function abdicate($payload, ConnectionInterface $conn) {
        if (!isset($payload["power"])) {
            throw new \Exception("No power to give up");
        }

        $gameName = ConnectionRegistry::GetGameById($conn->resourceId);
        $power = $this->sanitize($payload["power"]);

        $this->gameRunner->getGame($gameName)->abdicate($conn->resourceId, $power);

        return true;
    }

    public function __call($name, $arguments) {
        // Returning this error to the client through the top level exception handler is preferable to
        // PHP Fatal error:  Uncaught Error: Call to undefined method stdClass::lolwut() in Command line code:1

        throw new \Exception("Unknown method: $name");
    }
}
