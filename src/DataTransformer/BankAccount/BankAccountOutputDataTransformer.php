<?php

namespace App\DataTransformer\BankAccount;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\BankAccount\BankAccountOutput;
use App\Entity\BankAccount;

class BankAccountOutputDataTransformer implements DataTransformerInterface
{

    public function transform($object, string $to, array $context = [])
    {
        return new BankAccountOutput(
            $object->getIban(),
            $object->getBankName(),
            $object->getBankAccountHolderName(),
            $object->getAmount(),
            $object->getCreatedAt()->format('Y-m-d H:i:s'),
            $object->getUpdatedAt()?->format('Y-m-d H:i:s'),
            $object->getDeletedAt()?->format('Y-m-d H:i:s'),
        );
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return BankAccountOutput::class === $to && $data instanceof BankAccount;
    }
}