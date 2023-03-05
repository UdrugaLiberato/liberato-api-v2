<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\LocationRepository;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class UpdateLocationController {
  public function __construct(
      public LocationRepository $locationRepository,
      public CategoryRepository $categoryRepository,
  ) {}

  public function __invoke(string $id, Request $request) {
    $currentLocation = $this->locationRepository->find($id);
    $newCategory = $this->categoryRepository->find($request->toArray()["category"]);

    $currentLocation->setCategory($newCategory);

    return $currentLocation;
  }
}