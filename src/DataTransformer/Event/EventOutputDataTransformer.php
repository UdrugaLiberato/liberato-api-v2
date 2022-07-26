<?php

namespace App\DataTransformer\Event;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\Event\EventOutput;
use App\Entity\Calendar;

class EventOutputDataTransformer implements DataTransformerInterface
{
    /**
     * @param object $object
     * @param string $to
     * @param array<mixed> $context
     * @return EventOutput
     */
    public function transform($object, string $to, array $context = []): EventOutput
    {
        return new EventOutput(
            $object->getId(),
            $object->getSubject(),
            $object->getStartTime(),
        );
    }

    /**
     * @param object $data
     * @param string $to
     * @param array<mixed> $context
     * @return bool
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return EventOutput::class === $to && $data instanceof Calendar;
    }
}