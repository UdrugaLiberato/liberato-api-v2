<?php

namespace App\DataTransformer\Event;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\Event\EventOutput;
use App\Entity\Calendar;
use Symfony\Component\HttpFoundation\JsonResponse;

class EventOutputDataTransformer implements DataTransformerInterface
{

    public function transform($object, string $to, array $context = [])
    {
        return new EventOutput(
            $object->getId(),
            $object->getSubject(),
            $object->getStartTime(),
        );
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return EventOutput::class === $to && $data instanceof Calendar;
    }
}