<?php

declare(strict_types=1);

namespace App\DataTransformer\Event;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Entity\Calendar;

class EventInputDataTransformer implements DataTransformerInterface
{
    /**
     * @param object       $object
     * @param array<mixed> $context
     */
    public function transform($object, string $to, array $context = []): Calendar
    {
        $event = new Calendar();
        $event->setSubject($object->subject);
        $event->setStartTime($object->startTime);

        return $event;
    }

    /**
     * @param object       $data
     * @param array<mixed> $context
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Calendar) {
            return false;
        }

        return Calendar::class === $to && null !== ($context['input']['class'] ?? null);
    }
}
