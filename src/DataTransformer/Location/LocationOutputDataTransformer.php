<?php

declare(strict_types=1);

namespace App\DataTransformer\Location;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\Location\LocationOutput;
use App\Entity\Answer;
use App\Entity\Location;
use Doctrine\Common\Collections\ArrayCollection;

class LocationOutputDataTransformer implements DataTransformerInterface
{
    /**
     * @param object       $object
     * @param array<mixed> $context
     */
    public function transform($object, string $to, array $context = []): LocationOutput
    {
        return new LocationOutput(
            $this->getAnswerAndQuestions($object->getAnswers()),
            $object->getName(),
            $object->getStreet(),
            $object->getPhone(),
            $object->getEmail(),
            $object->getPublished(),
            $object->getFeatured(),
            $object->getCategory(),
            $object->getCity(),
            $object->getCreatedAt()->format('Y-m-d H:i:s'),
            $object->getUser()->getName(),
            $object->getAbout(),
            $object->getUpdatedAt()?->format('Y-m-d H:i:s'),
            $object->getDeletedAt()?->format('Y-m-d H:i:s'),
            $object->getImages()
        );
    }

    /**
     * @param object       $data
     * @param array<mixed> $context
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return LocationOutput::class === $to && $data instanceof Location;
    }

    private function getAnswerAndQuestions(ArrayCollection $answers): ArrayCollection
    {
        return $answers->map(static function (Answer $answer) {
            return [
                'question' => $answer->getQuestion(),
                'answer' => $answer->getAnswer(),
            ];
        });
    }
}
