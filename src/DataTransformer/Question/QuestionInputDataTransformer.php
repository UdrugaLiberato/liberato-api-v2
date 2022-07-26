<?php

namespace App\DataTransformer\Question;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Entity\Question;

class QuestionInputDataTransformer implements DataTransformerInterface
{
    /**
     * @param object $object
     * @param string $to
     * @param array<mixed> $context
     * @return Question
     */
    public function transform($object, string $to, array $context = []): Question
    {
        $question = new Question();
        $question->setQuestion($object->question);
        $question->setCategory($object->category);

        return $question;
    }

    /**
     * @param object $data
     * @param string $to
     * @param array<mixed> $context
     * @return bool
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Question) {
            return false;
        }

        return Question::class === $to && null !== ($context['input']['class'] ?? null);

    }
}