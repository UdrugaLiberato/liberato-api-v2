<?php

namespace App\DataTransformer\City;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Entity\City;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CityInputDataTransformer implements DataTransformerInterface
{
    public function __construct(private HttpClientInterface $client)
    {
    }

    public function transform($object, string $to, array $context = []): object
    {
        $response = $this->client->request("GET", "https://maps.googleapis.com/maps/api/place/findplacefromtext/json?input=" . $object->name . "&inputtype=textquery&=&fields=geometry&key=AIzaSyDGlqdh7h7Me5fC9WJojYoC_wvm-0CARco");
        $content = $response->toArray();
        $lat = $content["candidates"][0]["geometry"]["location"]["lat"];
        $lng = $content["candidates"][0]["geometry"]["location"]["lng"];
        $city = new City();
        $city->setName($object->name);
        $city->setLatitude($lat);
        $city->setLongitude($lng);

        return $city;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof City) {
            return false;
        }

        return City::class === $to && null !== ($context['input']['class'] ?? null);
    }
}