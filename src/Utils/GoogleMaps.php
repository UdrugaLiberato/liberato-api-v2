<?php

declare(strict_types=1);

namespace App\Utils;

use App\Exception\CoordinatesNotFound;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GoogleMaps implements GoogleMapsInterface {
  public function __construct(
      public string               $apiKey,
      private HttpClientInterface $client
  ) {
  }

  public function getCoordinateForCity(string $city): array {
    $response = $this->client->request('GET', 'https://maps.googleapis.com/maps/api/place/findplacefromtext/json?input=' . $city . '&inputtype=textquery&=&fields=geometry&key=' . $this->apiKey);
    $content = $response->toArray();

    $lat = $content['candidates'][0]['geometry']['location']['lat'];
    $lng = $content['candidates'][0]['geometry']['location']['lng'];

    return [
        'lat' => $lat,
        'lng' => $lng,
    ];
  }

  public function getCoordinateForStreet(string $street, string $city): array {
    $encoded = urlencode($street . ' ' . $city);
    $url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $encoded .
        '&fields=geometry&key=AIzaSyDGlqdh7h7Me5fC9WJojYoC_wvm-0CARco';
    $response = $this->client->request('GET', $url);
    $content = $response->toArray();
    if ($response->toArray()["status"] == "ZERO_RESULTS") {
      throw new CoordinatesNotFound(sprintf('Coordinates for "%s" could not be found.',
          $street . ' ' . $city));
    }

    $formatted_address = $content['results'][0]['formatted_address'];
    $lat = $content['results'][0]['geometry']['location']['lat'];
    $lng = $content['results'][0]['geometry']['location']['lng'];

    return [
        'lat' => $lat,
        'lng' => $lng,
        'formatted_address' => $formatted_address,
    ];
  }
}
