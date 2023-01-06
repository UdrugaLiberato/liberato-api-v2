<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Answer;
use App\Entity\Location;
use App\Repository\CategoryRepository;
use App\Repository\CityRepository;
use App\Repository\LocationRepository;
use App\Repository\QuestionRepository;
use App\Utils\GoogleMapsInterface;
use App\Utils\LiberatoHelperInterface;

class CreateLocationProcessor implements ProcessorInterface
{
    public function __construct(
        private LocationRepository      $locationRepository,
        private GoogleMapsInterface     $googleMaps,
        private CategoryRepository      $categoryRepository,
        private QuestionRepository      $questionRepository,
        private CityRepository          $cityRepository,
        private LiberatoHelperInterface $liberatoHelper
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Location
    {
        $image = $this->liberatoHelper->transformFiles($data->images, 'location/');
        ['lat' => $lat, 'lng' => $lng, 'formatted_address' => $formatted_address] =
            $this->googleMaps->getCoordinateForStreet($data->street,
                $data->city);
        $category = $this->categoryRepository->findOneBy(['name' => $data->category]);
        $city = $this->cityRepository->findOneBy(['name' => $data->city]);

        $location = new Location();
        $location->setName($data->name);
        $location->setStreet($formatted_address);
        $location->setLatitude($lat);
        $location->setLongitude($lng);
        $location->setCategory($category);
        $location->setCity($city);
        $location->setImages($image);
//        $items = explode(",", $data->qa);
//        foreach ($items as $item) {
//            [$q, $a] = explode(":", $item);
//            $qEntity = $this->questionRepository->find($q);
//            $answer = new Answer();
//            $answer->setQuestion($qEntity);
//            $answer->setAnswer($a);
//            $answer->setLocation($location);
//            $location->addAnswer($answer);
//        }

        $this->locationRepository->add($location);

//        dd($location->getAnswers());
        return $location;
    }


}
