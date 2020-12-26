<?php

namespace Axis;

use Ratchet\ConnectionInterface;

//this class needs to be initialized exactly one time
class ConnectionRegistry
{
    private static bool $initialized = false;
    protected static \SplObjectStorage $clients;
    //connection -> key
    protected static array $keys = [];
    //key -> context
    protected static array $context = [];

    public static function Init()
    {
        if (!self::$initialized) {
            self::$clients = new \SplObjectStorage();
            self::$initialized = true;
        }
    }

    public static function Add(ConnectionInterface $client)
    {
        self::$clients->attach($client);
        self::$context[$client->resourceId] = [];
    }

    public static function Remove(ConnectionInterface $client)
    {
        unset(self::$context[$client->resourceId]);
        self::$clients->detach($client);
    }

    public static function GetRawContext(int $id)
    {
        if (!isset(self::$keys[$id])) {
            return ['Not connected'];
        }

        return self::$context[self::$keys[$id]] ?? [];
    }

    public static function SetKey(int $id, string $key, array $context = [])
    {
        self::$keys[$id] = $key;

        if (!self::KeyExists($key)) {
            self::$context[$key] = $context;
        }
    }

    public static function KeyExists(string $key)
    {
        return isset(self::$context[$key]);
    }

    public static function ExpireKey(string $key)
    {
        unset(self::$context[$key]);
        //should revisit how this behaves with
        //currently logged in users at some point
    }

    public static function SetExpiry(int $id, int $expiry)
    {
        self::SetValue($id, 'expiry', $expiry);
    }

    public static function GetExpiryByKey(string $key)
    {
        return self::GetValueByKey($key, 'expiry');
    }

    public static function SetName(int $id, string $name)
    {
        self::SetValue($id, 'name', $name);
    }

    public static function GetNameById(int $id)
    {
        return self::GetValueById($id, 'name');
    }

    public static function SetGame(int $id, string $game)
    {
        self::SetValue($id, 'game', $game);
    }

    public static function GetGameById(int $id)
    {
        return self::GetValueById($id, 'game');
    }

    private static function SetValue(int $id, string $key, string $value)
    {
        if (isset(self::$keys[$id])) {
            self::$context[self::$keys[$id]][$key] = $value;
        }
    }

    private static function GetValueById(int $id, string $key)
    {
        if (isset(self::$keys[$id])) {
            return self::GetValueByKey(self::$keys[$id], $key);
        }

        return null;
    }

    private static function GetValueByKey(string $sessionKey, string $key)
    {
        if (isset(self::$context[$sessionKey][$key])) {
            return self::$context[$sessionKey][$key];
        }

        return null;
    }

    public static function GetById(int $id): ?ConnectionInterface
    {
        foreach (self::$clients as $client) {
            if ($client->resourceId == $id) {
                return $client;
            }
        }

        return null;
    }

    //$ids should be int, return value is an array of ConnectionInterfaces
    public static function GetListByIds(array $ids): array
    {
        $matches = [];
        foreach (self::$clients as $client) {
            if (in_array($client->resourceId, $ids)) {
                $matches[] = $client;
            }
        }

        return $matches;
    }
}
