<?php

namespace App\DataTransformer\Location;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\Exception\ValidationException;
use App\API\GoogleMapsInterface;
use App\Entity\Location;
use App\Repository\CategoryRepository;
use App\Repository\CityRepository;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LocationInputDataTransformer implements DataTransformerInterface
{

    public string $uploadDir;

    /**
     * @param KernelInterface $kernel
     * @param ValidatorInterface $validator
     * @param TokenStorageInterface $token
     * @param GoogleMapsInterface $googleMapsInterface
     * @param CityRepository $cityRepository
     * @param CategoryRepository $categoryRepository
     * @param TokenStorageInterface $token
     */
    public function __construct(public KernelInterface       $kernel,
                                public ValidatorInterface    $validator,
                                public TokenStorageInterface $token,
                                public GoogleMapsInterface   $googleMapsInterface,
                                public CityRepository        $cityRepository,
                                public CategoryRepository    $categoryRepository,
    )
    {
        $this->uploadDir = $this->kernel->getProjectDir() . "/public/images/locations/";
    }

    public function transform($object, string $to, array $context = []): object
    {
        $fileNames = $this->transformPictures($object->images);
        $city = $this->cityRepository->find($object->city);
        $category = $this->categoryRepository->find($object->category);
        [$streetName, $streetNumber] = explode(" ", $object->street);
        ["lat" => $lat, "lng" => $lng, "formatted_address" => $formatted_address] =
            $this->googleMapsInterface->getCoordinateForStreet
            ($streetNumber . " " . $streetName, $city->getName());

        $location = new Location();
        $location->setName($object->name);
        $location->setCity($city);
        $location->setUser($this->token?->getToken()?->getUser());
        $location->setCategory($category);
        $location->setStreet($formatted_address);
        $location->setImages($fileNames);
        $location->setPhone($object->phone);
        $location->setEmail($object->email);
        $location->setAbout($object->about);
        $location->setPublished($object->published);
        $location->setFeatured($object->featured);
        $location->setLatitude($lat);
        $location->setLongitude($lng);

        return $location;
    }

    private function transformPictures($uploadedFiles): array
    {
        $fileNames = [];
        if (!empty($uploadedFiles)) {
            foreach ($uploadedFiles as $file) {
                $errors = $this->validator->validate($file, new Image());
                if (count($errors) > 0) {
                    throw new ValidationException("Only images can be uploaded!");
                }
                $mime = $file->getMimeType();
                $originalFilename = pathinfo(
                    $file->getClientOriginalName(),
                    PATHINFO_FILENAME
                );
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $this->slugify($originalFilename);
                $newFilename = date('Y-m-d') . "_" . $safeFilename . md5
                    (
                        microtime()
                    ) . '.'
                    . $file->guessExtension();
                $file->move(
                    $this->uploadDir,
                    $newFilename
                );

                $F = file_get_contents($this->uploadDir . $newFilename);
                $base64 = base64_encode($F);
                $blob = 'data:' . $mime . ';base64,' . $base64;
                $fileObj = [
                    "path" => $newFilename,
                    "title" => $file->getClientOriginalName(),
                    "mime" => $mime,
                    "src" => $blob,
                ];
                $fileNames[] = $fileObj;
            }
            return $fileNames;
        }
        return [];
    }

    private function slugify(string $title): string
    {
        $title = preg_replace('~[^\pL\d]+~u', '-', $title);
        $title = iconv('utf-8', 'us-ascii//TRANSLIT', $title);
        $title = preg_replace('~[^-\w]+~', '', $title);
        $title = trim($title, '-');
        $title = preg_replace('~-+~', '-', $title);

        return strtolower($title);
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Location) {
            return false;
        }

        return Location::class === $to && null !== ($context['input']['class'] ?? null);
    }
}