<?php

declare(strict_types=1);

namespace App\Utils;

interface GoogleMapsInterface
{
    /**
     * @return array<float>
     */
    public function getCoordinateForCity(string $city): array;

    /**
     * @return array<mixed>
     */
    public function getCoordinateForStreet(string $street, string $city): array;
}
