<?php

namespace Axis;

use Exception;

class LobbyContainer
{
    private array $lobbies = [];

    public function exists(string $name) {
        return isset($this->lobbies[$name]);
    }

    public function add(array $payload) {
        $name = $this->sanitize($payload["name"]);

        if ($this->exists($name)) {
            throw new Exception("Lobby {$lobby->name()} already exists");
        }

        $this->lobbies[$name] = new Lobby($name);

        return ["name" => $name];
    }

    public function addUser(array $payload) {
        $lobby = $this->sanitize($payload["lobby"]);

        if (!$this->exists($lobby)) {
            throw new Exception("Lobby $name doesn't exist");
        }

        $this->lobbies[$lobby]->add($payload["connectionId"]);

        return true;
    }

    public function removeUser(array $payload) {
        $lobby = $this->sanitize($payload["lobby"]);

        if ($this->exists($lobby)) {
            $this->lobbies[$lobby]->remove($payload["connectionId"]);
        }

        return true;
    }

    public function remove(string $old) {
        unset($this->lobbies[$old]);
    }

    public function rename(array $payload) {
        $old = $payload["old"];
        $new = $payload["new"];

        if (!$this->exists($old)) {
            throw new Exception("Old lobby $old doesn't exist");
        }

        $new = $this->sanitize($new);

        if ($this->exists($new)) {
            throw new Exception("Lobby named $new already exists");
        }

        $this->lobbies[$new] = $this->lobbies[$old];
        unset($this->lobbies[$old]);

        return ["name" => $new];
    }

    public function message(array $payload) {
        $name = $this->sanitize($payload["lobby"]);

        if (!$this->exists($name)) {
            throw new Exception("Lobby $name doesn't exist");
        }

        $this->lobbies[$name]->message($payload);
    }

    public function sanitize(?string $name = null) {
        if (is_null($name)) {
            return "lobby-" + substr(md5(mt_rand()), 0, 5);
        } else {
            return preg_replace("/[^a-zA-Z0-9\-]/", "", $name);
        }
    }
}