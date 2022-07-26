<?php

namespace App\DataTransformer\Category;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Entity\Category;
use App\Utils\LiberatoHelperInterface;


class CategoryInputDataTransformer implements DataTransformerInterface
{
    public function __construct(public LiberatoHelperInterface $liberatoHelper,
    )
    {
    }

    /**
     * @param object $object
     * @param string $to
     * @param array<mixed> $context
     * @return Category
     */
    public function transform($object, string $to, array $context = []): Category
    {
        $category = new Category();
        $category->setName($object->name);
        $category->setIcon($this->liberatoHelper->transformImage($object->file, "category"));
        $category->setDescription($object->description);

        return $category;
    }

    /**
     * @param object $data
     * @param string $to
     * @param array<mixed> $context
     * @return bool
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Category) {
            return false;
        }

        return Category::class === $to && null !== ($context['input']['class'] ?? null);
    }

}