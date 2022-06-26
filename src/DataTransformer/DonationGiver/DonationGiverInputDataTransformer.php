<?php

namespace App\DataTransformer\DonationGiver;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Entity\DonationGiver;
use App\Repository\BankAccountRepository;
use DateTimeImmutable;

class DonationGiverInputDataTransformer implements DataTransformerInterface
{

    public function __construct(private BankAccountRepository $bankAccountRepository)
    {
    }

    public function transform($object, string $to, array $context = [])
    {
        $account = $this->bankAccountRepository->findAll()[0];
        $account->setAmount($object->moneyGiven);
        $account->setUpdatedAt(new DateTimeImmutable("now"));
        $this->bankAccountRepository->add($account);
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