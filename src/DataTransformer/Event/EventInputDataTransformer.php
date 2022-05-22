<?php

namespace App\DataTransformer\Event;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Entity\Calendar;

class EventInputDataTransformer implements DataTransformerInterface
{

    public function transform($object, string $to, array $context = [])
    {
        $event = new Calendar();
        $event->setSubject($object->subject);
        $event->setStartTime($object->startTime);

        return $event;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Calendar) {
            return false;
        }

        return Calendar::class === $to && null !== ($context['input']['class'] ?? null);
    }
}