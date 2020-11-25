<?php

namespace Axis;

use Ratchet\ConnectionInterface;

//this class needs to be initialized exactly one time
class ConnectionRegistry {
    private static bool $initialized = false;
    protected static \SplObjectStorage $clients;
    protected static array $context = [];

    public static function Init() {
        if (!self::$initialized) {
            self::$clients = new \SplObjectStorage();
            self::$initialized = true;
        }
    }

    public static function Add(ConnectionInterface $client) {
        self::$clients->attach($client);
        self::$context[$client->resourceId] = [];
    }

    public static function Remove(ConnectionInterface $client) {
        unset(self::$context[$client->resourceId]);
        self::$clients->detach($client);
    }

    public static function SetName(int $id, string $name) {
        self::SetValue($id, "name", $name);
    }

    public static function GetNameById(int $id) {
        return self::GetValueById($id, "name");
    }

    public static function SetGame(int $id, string $game) {
        self::SetValue($id, "game", $game);
    }

    public static function GetGameById(int $id) {
        return self::GetValueById($id, "game");
    }

    private static function SetValue(int $id, string $key, string $value) {
        if (isset(self::$context[$id])) {
            self::$context[$id][$key] = $value;
        }
    }

    private static function GetValueById(int $id, string $key) {
        if (isset(self::$context[$id][$key])) {
            return self::$context[$id][$key];
        }

        return null;
    }

    public static function GetById(int $id) : ?ConnectionInterface {
        foreach (self::$clients as $client) {
            if ($client->resourceId == $id) {
                return $client;
            }
        }

        return null;
    }

    //$ids should be int, return value is an array of ConnectionInterfaces
    public static function GetListByIds(array $ids) : array {
        $matches = [];
        foreach (self::$clients as $client) {
            if (in_array($client->resourceId, $ids)) {
                $matches[] = $client;
            }
        }

        return $matches;
    }
}