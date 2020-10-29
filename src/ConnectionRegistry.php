<?php

namespace Axis;

use Ratchet\ConnectionInterface;

//this class needs to be initialized exactly one time
class ConnectionRegistry {
    private static bool $initialized = false;
    protected static \SplObjectStorage $clients;

    public static function Init() {
        if (!$initialized) {
            $this->clients = new \SplObjectStorage();
            $initialized = true;
        }
    }

    public static function Add(ConnectionInterface $client) {
        $this->clients->attach($client);
    }

    public static function Remove(ConnectionInterface $client) {
        $this->clients->detach($client);
    }

    public static function GetById(int $id) : ?ConnectionInterface {
        foreach ($this->clients as $client) {
            if ($client->resourceId == $id) {
                return $client;
            }
        }

        return null;
    }

    //$ids should be int, return value is an array of ConnectionInterfaces
    public static function GetListByIds(array $ids) : array {
        $matches = [];
        foreach ($this->clients as $client) {
            if (in_array($client->resourceId, $ids)) {
                $matches[] = $client;
            }
        }

        return $matches;
    }
}