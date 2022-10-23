<?php

declare(strict_types=1);

namespace App\DataTransformer\Location;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\Location\LocationInput;
use App\Entity\Answer;
use App\Entity\Category;
use App\Entity\City;
use App\Entity\Location;
use App\Repository\CategoryRepository;
use App\Repository\CityRepository;
use App\Utils\GoogleMapsInterface;
use App\Utils\LiberatoHelperInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class LocationInputDataTransformer implements DataTransformerInterface
{
    public function __construct(
        public LiberatoHelperInterface $liberatoHelper,
        public TokenStorageInterface   $token,
        public GoogleMapsInterface     $googleMapsInterface,
        public CityRepository          $cityRepository,
        public CategoryRepository      $categoryRepository,
    )
    {
    }

    /**
     * @param LocationInput $object
     * @param array<mixed> $context
     */
    public function transform($object, string $to, array $context = []): Location
    {
        $fileNames = $this->liberatoHelper->transformImages($object->images, 'locations');
        [$streetName, $streetNumber] = explode(' ', $object->street);
        $city = $this->getCity($object->city);
        $category = $this->getCategory($object->category);

        ['lat' => $lat, 'lng' => $lng, 'formatted_address' => $formatted_address] =
            $this->googleMapsInterface->getCoordinateForStreet($streetNumber . ' ' . $streetName, $city->getName());

        $location = new Location();
        $location->setName($object->name);
        $location->setCity($city);
        $location->setCategory($category);
        $location->setStreet($formatted_address);
        $location->setImages($fileNames);
        $location->setPhone($object->phone);
        $location->setEmail($object->email);
        $location->setAbout($object->about);
        $location->setPublished('true' === $object->published);
        $location->setFeatured('true' === $object->featured);
        $location->setLatitude($lat);
        $location->setLongitude($lng);

        $this->addAnswers($object, $location);

        return $location;
    }

    private function getCity(string $id): City
    {
        return $this->cityRepository->find($id);
    }

    private function getCategory(string $id): Category
    {
        return $this->categoryRepository->find($id);
    }

    private function addAnswers(LocationInput $object, Location $location): void
    {
        $answerArr = explode(',', $object->answers);
        foreach ($answerArr as $answer) {
            [$question, $answer] = explode(':', $answer);
            $Answer = new Answer();
            $Answer->setQuestion($question);
            $Answer->setAnswer((bool)$answer);
            $location->addAnswer($Answer);
        }
    }

    /**
     * @param object $data
     * @param array<mixed> $context
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Location) {
            return false;
        }

        return Location::class === $to && null !== ($context['input']['class'] ?? null);
    }
}
