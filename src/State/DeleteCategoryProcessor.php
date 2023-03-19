<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Exception\CategoryHasLocationsException;
use App\Repository\CategoryRepository;
use DateTimeImmutable;

class DeleteCategoryProcessor implements ProcessorInterface {
  public function __construct(
      private CategoryRepository $categoryRepository,
  ) {
  }

  public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void {
    $category = $this->categoryRepository->find($data->getId());
    if ($category->getLocations()->count() > 0) {
      throw new CategoryHasLocationsException(sprintf('Category %s has locations. So you can not delete it',
          $category->getName()));
    }
    $category->setDeletedAt(new DateTimeImmutable());
    $this->categoryRepository->save($category, true);
  }
}
