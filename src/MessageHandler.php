<?php

namespace Axis;

use Monolog\Logger;

class MessageHandler {
    private $logger;
    protected $users;
    protected $lobbies;

    public function __construct(Logger $logger) {
        $this->logger = $logger;

        $this->users = new Users($this->logger);
        $this->lobbies = new LobbyContainer();
    }

    public function handle(array $message) {
        $payload = $message["payload"];

        switch($message["method"]) {
            case "lobbyMessage":
                $this->lobbies->message($payload);
                break;
            default:
                throw new Exception("what?");
        }
    }
}
