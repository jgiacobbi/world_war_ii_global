<?php

namespace Axis;

use Monolog\Logger;

class Users {
    private string $path;
    private $users;
    private $logger;

    public function __construct(Logger $logger) {
        $this->logger = $logger;
        $this->path = dirname(__dir__) . "/files/users.json";
        $json = "";

        if (file_exists($this->path)) {
            $json = file_get_contents($this->path);
        } else {
            $json = json_encode([]);
            file_put_contents($this->path, $json);
        }

        $users = json_decode($json, true);

        if (is_null($users)) {
            $this->logger->warning("Invalid user json: $json");
            file_put_contents($this->path, "[]");
            $this->users = [];
        } else {
            $this->users = $users;
        }
    }

    public function deleteUser($username) {
        if ($this->userExists($username)) {
            unset($this->users[$username]);
            file_put_contents(json_encode($this->users));
        }
    }

    public function addUser($username, $password) {
        if ($this->userExists($username)) {
            $this->logger->error("User $username already exists");
            throw new \Exception("User $username already exists");
        }

        $this->users[$username] = md5($password);
        file_put_contents(json_encode($this->users));
    }

    public function userExists($username) {
        return isset($this->users[$username]);
    }

    public function checkUser($username, $password) {
        if (!$this->userExists($username)) {
            return false;
        }

        return $this->users[$username] == md5($password);
    }
}