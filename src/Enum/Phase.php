<?php

namespace Axis\Enum;

use Axis\Globals;
use Eloquent\Enumeration\AbstractMultiton;

class Phase extends AbstractMultiton {

    protected int $order;

    protected function __construct(string $key, int $order)
    {
        parent::__construct($key);
        $this->order = $order;
    }

    public function order(): int
    {
        return $this->order;
    }

    public function after(self $other): bool
    {
        return $this->order() > $other->order();
    }

    public function before(self $other): bool
    {
        return $this->order() < $other->order();
    }

    protected static array $phases = [
        "research",
        "repair",
        "buy",
        "combat",
        "dice",
        "non-combat",
        "place",
        "collect"
    ];

    protected static function initializeMembers()
    {
        $index = 0;

        foreach(static::$phases as $phase) {
            new static(strtoupper($phase), $index);
            $index++;
        }
    }
}