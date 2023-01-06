<?php

namespace App\Controller;

use App\Entity\Image;
use App\Repository\ImageRepository;
use App\Repository\LocationRepository;
use App\Utils\LiberatoHelper;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class ImportImagesController
{
    public const BACKEND_URL_IMAGES = 'https://dev.udruga-liberato.hr/images/';
    public string $uploadDir;

    public function __construct(
        public KernelInterface $kernel,
        private LocationRepository $locationRepository,
        private ImageRepository $imageRepository,
    )
    {
        $this->uploadDir = $this->kernel->getProjectDir() . '/public/images/';
    }

    #[Route('/import', methods: ['POST'])]
    public function importImage(Request $request): JsonResponse
    {
        foreach ($request->files->get("image") as $file) {
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
            $location = $this->locationRepository->findAll()[0];
            $image = new Image();
            $image->setSrc(self::BACKEND_URL_IMAGES . "locations/" . $newFilename);
            $image->setName($safeFilename);
            $image->setMime($mime);
            $image->addLocation($location);
            $this->imageRepository->save($image, true);
            $location->addImage($image);
        }

        return new JsonResponse(['success' => true]);
    }
}