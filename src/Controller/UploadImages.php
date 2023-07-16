<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Image;
use App\Repository\ImageRepository;
use App\Utils\LiberatoHelper;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class UploadImages {
  public const BACKEND_URL_IMAGES = 'https://dev.udruga-liberato.hr/uploads/';
  public string $uploadDir;

  public function __construct(
      public KernelInterface  $kernel,
      private ImageRepository $imageRepository,
  ) {
    $this->uploadDir = $this->kernel->getProjectDir() . '/public/uploads/';
  }

  #[Route('/upload', methods: ['POST'])]
  public function upload(Request $request): JsonResponse {
    $file = $request->files->get('image');
    $ext = $file->guessExtension();
    $mime = $file->getMimeType();
    $originalFilename = pathinfo(
        $file->getClientOriginalName(),
        PATHINFO_FILENAME
    );
    // this is needed to safely include the file name as part of the URL
    $safeFilename = LiberatoHelper::slugify($originalFilename);
    $newFilename = date('Y-m-d') . '_' . $safeFilename . '.'
        . $ext;
    $file->move(
        $this->uploadDir . 'posts//',
        $newFilename
    );

    $image = new Image();
    $image->setSrc(self::BACKEND_URL_IMAGES . 'posts/' . $newFilename);
    $image->setName($safeFilename);
    $image->setMime($mime);
    $this->imageRepository->save($image, true);

    return new JsonResponse([
        'success' => true,
        'image'   => [
            'id'   => $image->getId(),
            'src'  => $image->getSrc(),
            'name' => $image->getName(),
            'mime' => $image->getMime(),
        ]
    ]);
  }
}
