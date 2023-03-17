<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Location;
use App\Repository\AnswerRepository;
use App\Repository\CategoryRepository;
use App\Repository\ImageRepository;
use App\Repository\LocationRepository;
use App\Utils\GoogleMapsInterface;
use App\Utils\LiberatoHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsController]
class UpdateLocationController {
  public const BACKEND_URL_IMAGES = 'https://dev.udruga-liberato.hr/images/';
  public string $uploadDir;

  public function __construct(
      public KernelInterface     $kernel,
      public ImageRepository     $imageRepository,
      public LocationRepository  $locationRepository,
      public CategoryRepository  $categoryRepository,
      public AnswerRepository    $answerRepository,
      public GoogleMapsInterface $googleMaps,
  ) {
    $this->uploadDir = $this->kernel->getProjectDir() . '/public/images/';
  }

  public function __invoke(string $id, Request $request) {
    $currentLocation = $this->locationRepository->find($id);

    if ($request->get("category") != $currentLocation->getCategory()->getId()) {
      $newCategory = $this->categoryRepository->find($request->get("category"));
      $currentLocation->setCategory($newCategory);
    }

    if ($request->get("name") != $currentLocation->getName()) {
      $currentLocation->setName($request->get("name"));
    }

    if ($request->get("about") != $currentLocation->getAbout() && $request->get("about") != "undefined") {
      $currentLocation->setAbout($request->get("about"));
    }

    if ($request->get("email") != $currentLocation->getEmail() && $request->get("email") != "undefined") {
      $currentLocation->setEmail($request->get("email"));
    }

    if ($request->get("phone") != $currentLocation->getPhone() && $request->get("phone") != "undefined") {
      $currentLocation->setPhone($request->get("phone"));
    }

    if ($request->get("street") != $currentLocation->getStreet()) {
      ['lat' => $lat, 'lng' => $lng, 'formatted_address' => $formatted_address] =
          $this->googleMaps->getCoordinateForStreet(
              $request->get("street"),
              $request->get("city"),
          );

      $currentLocation->setStreet($formatted_address);
      $currentLocation->setLatitude($lat);
      $currentLocation->setLongitude($lng);
    }

    if ($request->files->get("image")) {
      $this->cleanAllImages($currentLocation);
      $file = $request->files->get("image");
      $currentLocation->getImages()->clear();
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
          $this->uploadDir . 'locations/',
          $newFilename
      );
      $image = new Image();
      $image->setSrc(self::BACKEND_URL_IMAGES . 'locations/' . $newFilename);
      $image->setName($safeFilename);
      $image->setMime($mime);
      $image->addLocation($currentLocation);
      $this->imageRepository->save($image, true);
      $currentLocation->addImage($image);
    }

    if ($request->get("qa")) {
      $items = explode(',', $request->get("qa"));
      foreach ($items as $item) {
        [$answerId, $answer] = explode(':', $item);
        $answerEntity = $this->answerRepository->find($answerId);
        $answerEntity->setAnswer($answer === 'true');
        $answerEntity->setLocation($currentLocation);
      }
    }

    return $currentLocation;
  }

  private function
  cleanAllImages(Location $currentLocation): void {
    foreach ($currentLocation->getImages() as $image) {
      $currentLocation->removeImage($image);
    }
  }
}