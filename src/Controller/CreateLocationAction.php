<?php

namespace App\Controller;

use ApiPlatform\Core\Validator\Exception\ValidationException;
use App\Entity\Location;
use App\Repository\CategoryRepository;
use App\Repository\CityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsController]
class CreateLocationAction extends AbstractController
{
    public function __construct(
        private CityRepository $cityRepository,
        private CategoryRepository $categoryRepository
    )
    {
    }

    public function __invoke(
        Request            $request,
        ManagerRegistry    $doctrine,
        ValidatorInterface $validator
    ): Location
    {
        $entityManager = $doctrine->getManager();
        $uploadDir = $this->getParameter('location_images');
        $location = new Location();
        $cityId = explode("/",$request->get("city"));
        $categoryId = explode("/",$request->get("category"));

        $city = $this->cityRepository->findOneBy(["id" => $cityId]);
        $category = $this->categoryRepository->findOneBy(["id" => $categoryId]);

        $location->setName($request->get("name"));
        $location->setStreet($request->get("street"));
        $location->setPhone($request->get("phone"));
        $location->setEmail($request->get("email"));
        $location->setAbout($request->get("about"));
        $location->setCity($city);
        $location->setCategory($category);

        $entityManager->persist($location);

        $images = $request->files->get("images");
        $fileNames = [];
        if (!empty($images)) {
            foreach ($images as $key => $file) {
                $errors = $validator->validate($file, new Image());
                if (count($errors) > 0) {
                    throw new ValidationException("Only images can be uploaded!");
                }
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
                    $uploadDir,
                    $newFilename
                );
                $fileNames[$key] = $newFilename;
            }
            $location->setImages($fileNames);
            $entityManager->persist($location);
        }

        $entityManager->flush();
        return $location;
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
}