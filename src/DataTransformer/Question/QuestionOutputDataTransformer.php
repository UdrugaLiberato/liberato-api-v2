<?php

declare(strict_types=1);

namespace App\DataTransformer\Question;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\Question\QuestionOutput;
use App\Entity\Question;

class QuestionOutputDataTransformer implements DataTransformerInterface
{
    /**
     * @param object       $object
     * @param array<mixed> $context
     */
    public function transform($object, string $to, array $context = []): QuestionOutput
    {
        return new QuestionOutput(
            $object->getQuestion(),
            $object->getCategory(),
        );
    }

    /**
     * @param object       $data
     * @param array<mixed> $context
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return QuestionOutput::class === $to && $data instanceof Question;
    }
}
