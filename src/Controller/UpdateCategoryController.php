<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Utils\LiberatoHelperInterface;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class UpdateCategoryController
{
    public function __construct(
        private LiberatoHelperInterface $liberatoHelper,
        private CategoryRepository      $categoryRepository,
    )
    {
    }

    public function __invoke(string $id, Request $request): Category
    {
        $categoryToUpdate = $this->categoryRepository->find($id);
        $file = $this->liberatoHelper->getImagePath('category/') . $categoryToUpdate->getIcon()->first();
        if (file_exists($file)) {
            unlink($file);
        }

        if ($request->get('name') && $request->get('name') !== $categoryToUpdate->getName()) {
            $categoryToUpdate->setName($request->get('name'));
        }

        if ($request->get('description') && $request->get('description') !==
            $categoryToUpdate->getDescription()) {
            $categoryToUpdate->setDescription($request->get('description'));
        }
        $icon = $this->liberatoHelper->transformImage($request->files->get('file'), 'category');
        $categoryToUpdate->setIcon($icon);
        $categoryToUpdate->setUpdatedAt(new DateTimeImmutable('now'));
        $this->categoryRepository->update($categoryToUpdate);

        return $categoryToUpdate;
    }
}
