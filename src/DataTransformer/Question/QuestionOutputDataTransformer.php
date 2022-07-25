<?php

namespace App\DataTransformer\Question;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\Question\QuestionOutput;
use App\Entity\Question;

class QuestionOutputDataTransformer implements DataTransformerInterface
{


    public function transform($object, string $to, array $context = [])
    {
        return new QuestionOutput(
            $object->getQuestion(),
            $object->getCategory(),
        );
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return QuestionOutput::class === $to && $data instanceof Question;
    }
}