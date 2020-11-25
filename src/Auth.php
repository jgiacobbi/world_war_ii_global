<?php

namespace Axis;

use Monolog\Logger;

class Auth {
    private Logger $logger;
    private array $keys = [];

    public function __construct(Logger $logger) {
        $this->logger = $logger;
    }

    public function login(array $payload) : array {
        if (isset($payload["username"])) {
            return $this->loginWithCredentials($payload["username"]);
        } else if (isset($payload["key"])){
            return $this->loginWithKey($payload["key"]);
        }

        throw new \Exception("Useless login information provided");
    }

    public function loginWithCredentials(string $username) : array {
        $expiry = time() + 86400; //24h

        do {
            $key = uniqid("", true);
        } while (isset($this->keys[$key]));

        $this->keys[$key] = ["name" => $username, "expiry" => $expiry];
        return ["name" => $username, "key" => $key, "expiry" => $expiry];
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