<?php

namespace Axis;

use Monolog\Logger;

class Games {
    private Logger $logger;
    private string $games;
    private string $data;

    public function __construct(Logger $logger, string $files, string $data) {
        $this->logger = $logger;
        $this->games = $files . "/games";
        $this->data = $data;
        @mkdir($this->games, 0777, true);
    }

    //probably going to want some kind of caching in this class
    public function list() : array {
        return array_values(array_diff(scandir($this->games), ['.', '..']));
    }

    public function exists(string $name) {
        return in_array($name, $this->list());
    }

    public function createGame(string $name) {
        $folder = $this->gameDir($name);
        @mkdir($folder, 0777, true);
        copy($this->data . "/placements.json", "$folder/placements.json");
        copy($this->data . "/state.json", "$folder/state.json");

        $this->logger->info("Created game $name");
    }

    public function deleteGame(string $name) {
        $folder = gameDir($name);

        $files = glob('$folder/*'); // get all file names
        foreach($files as $file){
            if(is_file($file)) {
                unlink($file);
            }
        }

        rmdir($folder);
    }

    public function getPlacements(string $name) {
        return $this->getJson($this->gameDir($name) . "/placements.json");
    }

    private function getJson(string $file) {
        return json_decode(file_get_contents($file), true);
    }

    private function gameDir(string $name) : string {
        return $this->games . "/$name";
    }
}