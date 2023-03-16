<?php

namespace App\Controller;

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
    dd($request->toArray()["category"]);
    $newCategory = $this->categoryRepository->find($request->toArray()["category"]);
  $str = explode(":", $request->toArray()["qa"]);

  $answer = $this->answerRepository->find($str[0]);
  $answer->setAnswer(false);
    dd($answer);
    $currentLocation->setCategory($newCategory);

    return $currentLocation;
  }
}