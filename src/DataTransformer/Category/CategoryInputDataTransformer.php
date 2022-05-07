<?php

namespace App\DataTransformer\Category;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Entity\Category;

class CategoryInputDataTransformer implements DataTransformerInterface
{

    public function transform($object, string $to, array $context = []): object
    {
        $category = new Category();
        $category->setName($object->name);
        $category->setFile($object->file);
        $category->setDescription($object->description);

        return $category;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Category) {
            return false;
        }

        return Category::class === $to && null !== ($context['input']['class'] ?? null);
    }
}