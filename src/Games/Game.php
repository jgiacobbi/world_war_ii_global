<?php

namespace Axis\Games;

use Axis\Enum\Power;
use Axis\Log;

/**
 * This class represents a game in progress, and tracks current players.
 */
class Game
{
    private StorageInterface $storage;
    private string $name;
    private array $players = [];
    private array $powers = [];

    public function __construct(string $name, StorageInterface $storage)
    {
        $this->name = $name;
        $this->storage = $storage;
        foreach (Power::members() as $member) {
            $this->powers[] = $member->value();
        }
    }

    public function create()
    {
        if (!$this->storage->exists($this->name)) {
            $this->storage->createGame($this->name);
        }
    }

    public function delete()
    {
        $this->storage->deleteGame($this->name);
    }

    public function addPlayer(int $id)
    {
        if (isset($this->players[$id])) {
            throw new \Exception("Player with ID [$id] is already in the game");
        }

        $this->players[$id] = [];
        Log::info("Added [$id] to {$this->name}");
    }

    public function removePlayer(int $id)
    {
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

    public function getPlacements(): array
    {
        return $this->storage->getPlacements($this->name);
    }

    public function coronate(int $id, string $power)
    {
        if (!isset($this->powers[$power])) {
            throw new \Exception("Unknown power [$power]");
        }

        if (!isset($this->players[$id])) {
            throw new \Exception("Player $id is not in game {$this->name}");
        }

        if (!is_null($this->powers[$power])) {
            throw new \Exception("$power is already being controlled");
        }

        $this->powers[$power] = $id;
        $this->players[$id]->push($power);

        $name = ConnectionRegistry::GetNameById($id);
        Log::info("$power is now controlled by $name in game {$this->name}");
    }

    public function abdicate(int $id, string $power)
    {
        if (!isset($this->powers[$power])) {
            throw new \Exception("Unknown power [$power]");
        }

        if (is_null($this->powers[$power])) {
            return;
        }

        if ($this->powers[$power] != $id) {
            throw new \Exception('Cannot abdicate for another player');
        }

        $this->powers[$power] = null;

        //remove the power from the player's list of controlled powers
        if (isset($this->players[$id])) {
            $this->players[$id] = array_diff($this->players[$id], [$power]);
        }

        $name = ConnectionRegistry::GetNameById($id);
        Log::info("$power is no longer controlled by $name in game {$this->name}");
    }
}
