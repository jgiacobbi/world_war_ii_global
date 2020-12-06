<?php

namespace Axis\Games;

use Axis\Log;

/**
 * Storage layer for games
 */
class DiskStorage implements StorageInterface {
    /**
     * Where games are stored
     */
    private string $games;

    /**
     * Where immutable starting data is stored
     */
    private string $data;

    public function __construct(string $files, string $data) {
        $this->games = $files . "/games";
        $this->data = $data;
        @mkdir($this->games, 0777, true);
    }

    public function list() : array {
        return array_values(array_diff(scandir($this->games), ['.', '..']));
    }

    public function exists(string $name) : bool {
        return in_array($name, $this->list());
    }

    public function createGame(string $name) {
        $folder = $this->gameDir($name);
        @mkdir($folder, 0777, true);
        copy($this->data . "/placements.json", "$folder/placements.json");
        copy($this->data . "/state.json", "$folder/state.json");

        Log::info("Created game $name");
    }

    public function deleteGame(string $name) {
        $folder = gameDir($name);

        if (is_dir($folder)) {
            foreach(glob("$folder/*") as $file){
                if(is_file($file)) {
                    unlink($file);
                }
            }
    
            rmdir($folder);

            Log::info("Deleted game $name");
        }
    }

    public function getPlacements(string $name): array {
        return $this->getJson($this->gameDir($name) . "/placements.json");
    }

    private function getJson(string $file) {
        return json_decode(file_get_contents($file), true);
    }

    private function gameDir(string $name) : string {
        return $this->games . "/$name";
    }
}
