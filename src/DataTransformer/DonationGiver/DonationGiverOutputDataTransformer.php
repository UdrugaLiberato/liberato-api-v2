<?php

declare(strict_types=1);

namespace App\DataTransformer\DonationGiver;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\DonationGiver\DonationGiverOutput;
use App\Entity\DonationGiver;

class DonationGiverOutputDataTransformer implements DataTransformerInterface
{
    /**
     * @param DonationGiver $object
     * @param array<mixed>  $context
     */
    public function transform($object, string $to, array $context = []): DonationGiverOutput
    {
        return new DonationGiverOutput(
            $object->getName(),
            $object->getApproved(),
            $object->getMoneyRequested(),
            $object->getMoneyGiven(),
            $object->getDateOfApplication()->format('Y-m-d H:i:s'),
            $object->getDateOfApproval()?->format('Y-m-d H:i:s'),
            $object->getCreatedAt()->format('Y-m-d H:i:s'),
            $object->getUpdatedAt()?->format('Y-m-d H:i:s'),
            $object->getDeletedAt()?->format('Y-m-d H:i:s')
        );
    }

    /**
     * @param object       $data
     * @param array<mixed> $context
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return DonationGiverOutput::class === $to && $data instanceof DonationGiver;
    }
}
