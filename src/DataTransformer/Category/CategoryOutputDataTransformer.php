<?php

namespace App\DataTransformer\Category;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\Category\CategoryOutput;
use App\Entity\Category;

class CategoryOutputDataTransformer implements DataTransformerInterface
{

    public function transform($object, string $to, array $context = [])
    {
        return new CategoryOutput(
            $object->getName(),
            $object->getIcon(),
            null === $object->getDescription() ? null : $object->getDescription,
            null === $object->getDeletedAt() ? null : $object->getDeletedAt()->format('Y-m-d H:i:s"'),
        );
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return CategoryOutput::class === $to && $data instanceof Category;
    }
}