<?php

namespace Axis;

use Monolog\Logger;

class Auth {
    private Logger $logger;
    private Users $users;
    private array $keys = [];

    public function __construct(Logger $logger) {
        $this->logger = $logger;
        $this->users = new Users($this->logger);
    }

    public function register(array $payload) : array {
        if (isset($payload["username"], $payload["password"])) {
            $this->users->addUser($payload["username"], $payload["password"]);
            return true;
        }

        throw new \Exception("Credentials not provided");
    }

    public function login(array $payload) : array {
        if (isset($payload["username"], $payload["password"])) {
            return loginWithCredentials($payload["username"], $payload["password"]);
        } else if (isset($payload["key"])){
            return loginWithKey($payload["key"]);
        }

        throw new \Exception("Useless login information provided");
    }

    public function loginWithCredentials(string $username, string $password) : array {
        if (!$this->users->userExists($username)) {
            throw new \Exception("User doesn't exist");
        } else if (!$this->users->checkUser($username, $password)) {
            throw new \Exception("Invalid password");
        } else {
            $expiry = time() + 86400; //24h

            do {
                $key = uniqid("", true);
            } while (isset($this->keys[$key]));

            $this->keys[$key] = ["name" => $username, "expiry" => $expiry];
            return ["name" => $username, "key" => $key, "expiry" => $expiry];
        }
    }

    public function loginWithKey(string $key) : array {
        if (isset($this->keys[$key])) {
            $time = time();

            if ($time > $this->keys[$key]["expiry"]) {
                unset($this->keys[$key]);
                throw new \Exception("Key expired");
            }

            $expiry = $time + 86400;
            $this->keys[$key]["expiry"] = $expiry;
            return ["name" => $username, "key" => $key, "expiry" => $expiry];
        } else {
            throw new \Exception("Key not found");
        }
    }
}