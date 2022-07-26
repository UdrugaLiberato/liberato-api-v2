<?php

namespace App\DataTransformer\Question;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\Question\QuestionOutput;
use App\Entity\Question;

class QuestionOutputDataTransformer implements DataTransformerInterface
{
    /**
     * @param object $object
     * @param string $to
     * @param array<mixed> $context
     * @return QuestionOutput
     */
    public function transform($object, string $to, array $context = []): QuestionOutput
    {
        return new QuestionOutput(
            $object->getQuestion(),
            $object->getCategory(),
        );
    }

    /**
     * @param object $data
     * @param string $to
     * @param array<mixed> $context
     * @return boolean
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return QuestionOutput::class === $to && $data instanceof Question;
    }
}