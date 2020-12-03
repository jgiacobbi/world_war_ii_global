<?php

namespace Axis\Games;

use Axis\Log;

/**
 * This class deals with the logic of running a game. Turns, phases,
 * validation, etc.
 */
class GameRunner {
    private StorageInterface $gameStorage;
    private array $games = [];

    public function __construct(StorageInterface $gameStorage){
        $this->gameStorage = $gameStorage;
    }

    public function storage() : StorageInterface {
        return $this->gameStorage;
    }

    public function newGame(string $name) {
        if (!isset($this->games[$name])) {
            $this->games[$name] = new Game($name, $this->gameStorage);
            $this->games[$name]->create();
        }

        return $this->games[$name];
    }

    public function list() {
        return array_keys($this->games);
    }

    public function getGame(string $name) : ?Game {
        if (!isset($this->games[$name])) {
            Log::warning("Request for game $name that doesn't exist");
            return null;
        }

        return $this->games[$name];
    }

    public function deleteGame(string $name) {
        (@$this->games[$name] ?? new Game($name, $gameStorage))->delete();
        unset($this->games[$name]);
    }
}
