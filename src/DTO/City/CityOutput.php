<?php

namespace App\DTO\City;

class CityOutput
{
    public function __construct(
        private string $name,
        private float  $latitude,
        private float  $longitude,
    )
    {
    }
}