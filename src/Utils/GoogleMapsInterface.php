<?php

namespace App\Utils;

interface GoogleMapsInterface
{
    /**
     * @param string $city
     * @return array<float>
     */
    public function getCoordinateForCity(string $city): array;

    /**
     * @param string $street
     * @param string $city
     * @return array<mixed>
     */
    public function getCoordinateForStreet(string $street, string $city): array;
}