<?php

namespace App\DataTransformer\DonationGiver;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Entity\DonationGiver;

class DonationGiverInputDataTransformer implements DataTransformerInterface
{

    public function transform($object, string $to, array $context = [])
    {
        $donationGiver = new DonationGiver();
        $donationGiver->setName($object->name);
        $donationGiver->setApproved($object->approved);
        $donationGiver->setMoneyRequested($object->moneyRequested);
        $donationGiver->setMoneyGiven($object->moneyGiven);
        $donationGiver->setDateOfApplication(new \DateTime($object->dateOfApplication));
        $donationGiver->setDateOfApproval($object->dateOfApproval);

        return $donationGiver;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof DonationGiver) {
            return false;
        }

        return DonationGiver::class === $to && null !== ($context['input']['class'] ?? null);
    }
}