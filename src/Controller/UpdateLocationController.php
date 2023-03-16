<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Repository\AnswerRepository;
use App\Repository\CategoryRepository;
use App\Repository\LocationRepository;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class UpdateLocationController {
  public function __construct(
      public LocationRepository $locationRepository,
      public CategoryRepository $categoryRepository,
      public AnswerRepository $answerRepository
  ) {}

  public function __invoke(string $id, Request $request) {
    $currentLocation = $this->locationRepository->find($id);

    if ($request->toArray()["category"] != $currentLocation->getCategory()->getId()) {
      $newCategory = $this->categoryRepository->find($request->toArray()["category"]);
      $currentLocation->setCategory($newCategory);
    }

    if ($request->toArray()["name"] != $currentLocation->getName()) {
      $currentLocation->setName($request->toArray()["name"]);
    }

  if($request->toArray()["about"] != $currentLocation->getAbout()) {
    $currentLocation->setAbout($request->toArray()["about"]);
  }

  if($request->toArray()["email"] != $currentLocation->getEmail()) {
    $currentLocation->setEmail($request->toArray()["email"]);
  }

  if ($request->toArray()["phone"] != $currentLocation->getPhone()) {
    $currentLocation->setPhone($request->toArray()["phone"]);
  }


  if($request->toArray()["street"] != $currentLocation->getStreet()) {
// fqwtch new info
  }
if ($request->files->get("image")) {
  $newImage = $request->files->get("image");
    }
    $items = explode(',', $request->toArray()["qa"]);
    foreach ($items as $item) {
      [$answerId, $answer] = explode(':', $item);
      $answerEntity = $this->answerRepository->find($answerId);
      $answerEntity->setAnswer($answer === 'true');
      $answerEntity->setLocation($currentLocation);
    }

    dd($currentLocation);

    return $currentLocation;
  }
}