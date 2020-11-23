<?php

namespace Axis;

class Lobby
{
    //array of int
    private array $members = [];
    private string $name;

    public function __construct(string $name) {
        $this->setName($name);
    }

    public function setName(string $name) {
        $this->name = $name;
    }

    public function name() {
        return $this->name;
    }

    public function add(int $id) {
        $this->members[$id] = $id;
    }

    public function remove(int $id) {
        if ($this->member($id)) {
            unset($this->members[$id]);
        }
    }

    public function member(int $id) {
        return isset($this->members[$id]);
    }

    public function message(array $payload, int $id) {
        if (!$this->member($id)) {
            throw new Exception("Connection Id $id is not a member of this room");
        }
        $message = $payload["message"];
        $recipients = ConnectionRegistry::GetListByIds(
            array_diff(array_values($members), [$id])
        );

        $response = json_encode(
            [
                "lobby" => $this->name,
                "from" => ConnectionRegistry::GetNameById($id) ?? "Unknown",
                "time" => time(),
                "message" => $message
            ]
        );

        foreach ($recipients as $conn) {
            $conn->send($response);
        }
    }
}