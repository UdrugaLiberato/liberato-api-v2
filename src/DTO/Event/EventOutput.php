<?php

namespace App\DTO\Event;

class EventOutput
{
    public function __construct(
    public string $id, public string $Subject, public string $StartTime
    )
    {
    }
}