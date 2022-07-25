<?php

namespace App\Utils;

interface GoogleMapsInterface
{
    public function getCoordinateForCity(string $city): array;

    public function getCoordinateForStreet(string $street, string $city): array;
}