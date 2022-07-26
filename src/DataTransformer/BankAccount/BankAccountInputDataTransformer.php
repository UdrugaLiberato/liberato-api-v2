<?php

declare(strict_types=1);

namespace App\DataTransformer\BankAccount;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\BankAccount\BankAccountInput;
use App\Entity\BankAccount;

class BankAccountInputDataTransformer implements DataTransformerInterface
{
    /**
     * @param BankAccountInput $object
     * @param array<mixed>     $context
     */
    public function transform($object, string $to, array $context = []): BankAccount
    {
        $account = new BankAccount();
        $account->setBankName($object->bankName);
        $account->setIban($object->iban);
        $account->setBankAccountHolderName($object->bankAccountHolderName);

        return $account;
    }

    /**
     * @param object       $data
     * @param array<mixed> $context
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof BankAccount) {
            return false;
        }

        return BankAccount::class === $to && null !== ($context['input']['class'] ?? null);
    }
}
