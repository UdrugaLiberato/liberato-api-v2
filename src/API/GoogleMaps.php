<?php

namespace App\API;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GoogleMaps implements GoogleMapsInterface
{

    public function __construct(private HttpClientInterface $client)
    {
    }

    public function getCoordinateForCity(string $city): array
    {
        $response = $this->client->request("GET", "https://maps.googleapis.com/maps/api/place/findplacefromtext/json?input=" . $city . "&inputtype=textquery&=&fields=geometry&key=AIzaSyDGlqdh7h7Me5fC9WJojYoC_wvm-0CARco");
        $content = $response->toArray();

        $lat = $content["candidates"][0]["geometry"]["location"]["lat"];
        $lng = $content["candidates"][0]["geometry"]["location"]["lng"];

        return [
            "lat" => $lat,
            "lng" => $lng
        ];
    }

    public function getCoordinateForStreet(string $street, string $city): array
    {
        $encoded = urlencode($street . " " . $city);
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $encoded .
            "&fields=geometry&key=AIzaSyBq4XzQTYxcqFKahd60xifRHft215gbwCk";
        $response = $this->client->request("GET", $url);
        $content = $response->toArray();
        $formatted_address = $content["results"][0]["formatted_address"];
        $lat = $content["results"][0]["geometry"]["location"]["lat"];
        $lng = $content["results"][0]["geometry"]["location"]["lng"];

        return [
            "lat" => $lat,
            "lng" => $lng,
            "formatted_address" => $formatted_address
        ];
    }
}