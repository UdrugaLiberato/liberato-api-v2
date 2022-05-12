<?php

namespace App\DataTransformer\Question;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Entity\Question;

class QuestionInputDataTransformer implements DataTransformerInterface
{
    public function transform($object, string $to, array $context = [])
    {
        $question = new Question();
        $question->setQuestion($object->question);
        $question->setCategory($object->category);

        return $question;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Question) {
            return false;
        }

        return Question::class === $to && null !== ($context['input']['class'] ?? null);

    }
}