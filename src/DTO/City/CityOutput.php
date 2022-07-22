<?php

namespace App\DTO\City;

class CityOutput
{
    public function __construct(
        public string $name,
        public float  $latitude,
        public float  $longitude,
        public string $createdAt
    )
    {
    }
}