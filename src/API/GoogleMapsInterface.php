<?php

namespace App\API;

use Symfony\Contracts\HttpClient\HttpClientInterface;

interface GoogleMapsInterface
{
    public function getCoordinateForCity(string $city): array;
    public function getCoordinateForStreet(string $street, string $city): array;
}