<?php

declare(strict_types=1);

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

    /**
     * @param object       $object
     * @param array<mixed> $context
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function transform($object, string $to, array $context = []): DonationGiver
    {
        $account = $this->bankAccountRepository->findAll()[0];
        $oldAmount = $account->getAmount();
        if ($object->moneyGiven) {
            $account->setAmount($oldAmount + $object->moneyGiven);
            $account->setUpdatedAt(new DateTimeImmutable('now'));
        }
        $this->bankAccountRepository->add($account);
        $donationGiver = new DonationGiver();
        $donationGiver->setName($object->name);
        $donationGiver->setApproved($object->approved);
        $donationGiver->setMoneyRequested($object->moneyRequested);
        null !== $object->moneyGiven ? $donationGiver->setMoneyGiven($object->moneyGiven) :
            $donationGiver->setMoneyGiven(0);
        $donationGiver->setDateOfApplication(new \DateTimeImmutable($object->dateOfApplication));
        $donationGiver->setDateOfApproval(new \DateTimeImmutable($object->dateOfApproval));

        return $donationGiver;
    }

    /**
     * @param object       $data
     * @param array<mixed> $context
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof DonationGiver) {
            return false;
        }

        return DonationGiver::class === $to && null !== ($context['input']['class'] ?? null);
    }
}
