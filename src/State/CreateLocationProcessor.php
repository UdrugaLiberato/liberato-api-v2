<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Answer;
use App\Entity\Image;
use App\Entity\Location;
use App\Repository\CategoryRepository;
use App\Repository\CityRepository;
use App\Repository\ImageRepository;
use App\Repository\LocationRepository;
use App\Repository\QuestionRepository;
use App\Repository\UserRepository;
use App\Utils\GoogleMapsInterface;
use App\Utils\LiberatoHelper;
use App\Utils\LiberatoHelperInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class CreateLocationProcessor implements ProcessorInterface
{
    public const BACKEND_URL_IMAGES = 'https://dev.udruga-liberato.hr/images/';
    public string $uploadDir;

    public function __construct(
        public KernelInterface $kernel,
        private LocationRepository      $locationRepository,
        private GoogleMapsInterface     $googleMaps,
        private CategoryRepository      $categoryRepository,
        private QuestionRepository      $questionRepository,
        private CityRepository          $cityRepository,
        private ImageRepository     $imageRepository,
        private UserRepository          $userRepository,
        private LiberatoHelperInterface $liberatoHelper
    )
    {
        $this->uploadDir = $this->kernel->getProjectDir() . '/public/images/';
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Location
    {
        ['lat' => $lat, 'lng' => $lng, 'formatted_address' => $formatted_address] =
            $this->googleMaps->getCoordinateForStreet($data->street,
                $data->city);
        $category = $this->categoryRepository->findOneBy(['name' => $data->category]);
        $city = $this->cityRepository->findOneBy(['name' => $data->city]);
        $user = $this->userRepository->findOneBy(['username' => $data->user]);

        $location = new Location();
        $location->setName($data->name);
        $location->setStreet($formatted_address);
        $location->setLatitude($lat);
        $location->setLongitude($lng);
        $location->setCategory($category);
        $location->setCity($city);
        $location->setUser($user);
        $location->setPublished(true);
        if ($data->images) {
            foreach ($data->images as $file) {
                $ext = $file->guessExtension();
                $mime = $file->getMimeType();
                if ('text/html' === $mime) {
                    continue;
                }
                $originalFilename = pathinfo(
                    $file->getClientOriginalName(),
                    PATHINFO_FILENAME
                );
                // this is needed to safely include the file name as part of the URL
                $safeFilename = LiberatoHelper::slugify($originalFilename);
                $newFilename = date('Y-m-d') . '_' . $safeFilename . '.'
                    . $ext;
                $file->move(
                    $this->uploadDir . "locations/",
                    $newFilename
                );
                $image = new Image();
                $image->setSrc(self::BACKEND_URL_IMAGES . "locations/" . $newFilename);
                $image->setName($safeFilename);
                $image->setMime($mime);
                $image->addLocation($location);
                $this->imageRepository->save($image, true);
                $location->addImage($image);
            }
        }
        if ($data->qa) {
            $items = explode(",", $data->qa);
            foreach ($items as $item) {
                [$q, $a] = explode(":", $item);
                $qEntity = $this->questionRepository->findOneBy(["category" => $category, "question" => $q]);
                $answer = new Answer();
                $answer->setQuestion($qEntity);
                $answer->setAnswer($a);
                $answer->setLocation($location);
                $location->addAnswer($answer);
            }
        }
        $this->locationRepository->add($location);

//        dd($location->getAnswers());
        return $location;
    }


}
