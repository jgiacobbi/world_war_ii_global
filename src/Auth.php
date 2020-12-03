<?php

namespace Axis;

/**
 * This class deals with logging in, saving and resuming sessions.
 * Right now no passwords are used and accounts are transient. If
 * that ever changes this is the place to start.
 */
class Auth {
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

        Log::info("Connection $id logged in as $username");

        return [
            "name" => $username,
            "key" => $key,
            "expiry" => $expiry,
            "inGame" => $this->userInGame($id)
        ];
    }

    public function loginWithKey(int $id, string $key) : array {
        if (!ConnectionRegistry::KeyExists($key)) {
            throw new \Exception("Key not found");
        }

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

        Log::info("Connection $id logged in as $username");

        return [
            "name" => $username,
            "key" => $key,
            "expiry" => $expiry,
            "inGame" => $this->userInGame($id)
        ];
    }

    private function userInGame(int $id) {
        return !is_null(ConnectionRegistry::GetGameById($id));
    }
}