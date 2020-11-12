<?php

namespace Axis;

use Ratchet\ConnectionInterface;

//this class needs to be initialized exactly one time
class ConnectionRegistry {
    private static bool $initialized = false;
    protected static \SplObjectStorage $clients;
    protected static array $names = [];

    public static function Init() {
        if (!self::$initialized) {
            self::$clients = new \SplObjectStorage();
            self::$initialized = true;
        }
    }

    public static function Add(ConnectionInterface $client) {
        self::$clients->attach($client);
    }

    public static function Remove(ConnectionInterface $client) {
        unset(self::$names[$client->resourceId]);
        self::$clients->detach($client);
    }

    public static function SetName(int $id, string $name) {
        if (self::GetById($id) != null) {
            self::$names[$id] = $name;
        }
    }

    public static function GetNameById(int $id) {
        if (isset(self::$$names[$id])) {
            return self::$names[$id];
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