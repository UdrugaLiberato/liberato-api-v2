<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Repository\CategoryRepository;

class DeleteCategoryProcessor implements ProcessorInterface
{
    public function __construct(
        private CategoryRepository $categoryRepository,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $category = $this->categoryRepository->find($data->getId());
        $category->setDeletedAt(new \DateTimeImmutable());
        $this->categoryRepository->save($category, true);
    }
}
