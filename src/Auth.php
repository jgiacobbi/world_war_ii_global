<?php

namespace Axis;

use Monolog\Logger;

class Auth {
    private Logger $logger;

    public function __construct(Logger $logger) {
        $this->logger = $logger;
    }

    public function login(int $id, array $payload) : array {
        if (isset($payload["username"])) {
            return $this->loginWithCredentials($id, $payload["username"]);
        } else if (isset($payload["key"])){
            return $this->loginWithKey($id, $payload["key"]);
        }

        throw new \Exception("Useless login information provided");
    }

    public function loginWithCredentials(int $id, string $username) : array {
        $expiry = time() + 86400; //24h

        do {
            $key = uniqid("", true);
        } while (ConnectionRegistry::KeyExists($key));

        ConnectionRegistry::SetKey($id, $key, 
            ["name" => $username, "expiry" => $expiry]);

        $this->logger->info("Connection $id logged in as $username");

        return [
            "name" => $username,
            "key" => $key,
            "expiry" => $expiry,
            "inGame" => $this->userInGame($id)
        ];
    }

    public function loginWithKey(int $id, string $key) : array {
        if (ConnectionRegistry::KeyExists($key)) {
            $time = time();

            $expiration = ConnectionRegistry::GetExpiryByKey($key);
            if ($time > $expiration) {
                ConnectionRegistry::ExpireKey($key);
                throw new \Exception("Key expired: $time > $expiration");
            }

            ConnectionRegistry::SetKey($id, $key);

            $expiry = $time + 86400;
            ConnectionRegistry::SetExpiry($id, $expiry);
            $username = ConnectionRegistry::GetNameById($id);

            $this->logger->info("Connection $id logged in as $username");

            return [
                "name" => $username,
                "key" => $key,
                "expiry" => $expiry,
                "inGame" => $this->userInGame($id)
            ];
        } else {
            throw new \Exception("Key not found");
        }
    }

    private function userInGame(int $id) {
        return !is_null(ConnectionRegistry::GetGameById($id));
    }
}