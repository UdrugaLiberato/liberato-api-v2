<?php

declare(strict_types=1);

namespace App\DataTransformer\BankAccount;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\BankAccount\BankAccountOutput;
use App\Entity\BankAccount;

class BankAccountOutputDataTransformer implements DataTransformerInterface
{
    /**
     * @param BankAccount  $object
     * @param array<mixed> $context
     */
    public function transform($object, string $to, array $context = []): BankAccountOutput
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

    /**
     * @param object       $data
     * @param array<mixed> $context
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return BankAccountOutput::class === $to && $data instanceof BankAccount;
    }
}
