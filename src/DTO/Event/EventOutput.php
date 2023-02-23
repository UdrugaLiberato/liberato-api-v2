<?php

declare(strict_types=1);

namespace App\DTO\Event;

class EventOutput
{
    public function __construct(
        public string $id,
        public string $Subject,
        public string $StartTime
    ) {
    }
}
