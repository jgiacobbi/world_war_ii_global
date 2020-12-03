<?php

namespace Axis\Games;

interface StorageInterface {
    public function list(): array;
    public function exists(string $name): bool;
    public function createGame(string $name);
    public function deleteGame(string $name);
    public function getPowers(): array;
    public function getPlacements(string $name): array;
}