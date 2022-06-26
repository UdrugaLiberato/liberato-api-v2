<?php

namespace App\DataTransformer\DonationGiver;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\DonationGiver\DonationGiverOutput;
use App\Entity\DonationGiver;

class DonationGiverOutputDataTransformer implements DataTransformerInterface
{

    public function transform($object, string $to, array $context = [])
    {
        return new DonationGiverOutput(
            $object->getName(),
            $object->getApproved(),
            $object->getMoneyRequested(),
            $object->getMoneyGiven(),
            $object->getDateOfApplication()->format("Y-m-d H:i:s")
            ,
            $object->getDateOfApproval()?->format("Y-m-d H:i:s"),
            $object->getCreatedAt()->format('Y-m-d H:i:s'),
            $object->getUpdatedAt()?->format("Y-m-d H:i:s"),
            $object->getDeletedAt()?->format("Y-m-d H:i:s")
        );
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return DonationGiverOutput::class === $to && $data instanceof DonationGiver;
    }
}