<?php

namespace Axis\Games;

use Axis\Log;

/**
 * This class represents a game in progress, and tracks current players
 */
class Game {
    private StorageInterface $storage;
    private string $name;
    private array $players = [];
    private array $powers = [];

    public function __construct(string $name, StorageInterface $storage) {
        $this->name = $name;
        $this->storage = $storage;
        $this->powers = array_fill_keys($storage->getPowers(), null);
    }

    public function create() {
        if (!$this->storage->exists($this->name)) {
            $this->storage->create($this->name);
        }
    }

    public function delete() {
        $this->storage->delete($this->name);
    }

    public function addPlayer(int $id) {
        if (isset($this->players[$id])) {
            throw new \Exception("Player with ID [$id] is already in the game");
        }

        $this->players[$id] = [];
        Log::info("Added [$id] to {$this->name}");
    }

    public function removePlayer(int $id) {
        if (!isset($this->players[$id])) {
            return;
        }

        $controlled = $this->players[$id];
        $this->players[$id] = [];
        foreach ($controlled as $power) {
            $this->powers[$power] = null;
        }

        Log::info("Removed [$id] from {$this->name}");
    }

    public function getPlacements(): array {
        return $this->storage->getPlacements($this->name);
    }
}
