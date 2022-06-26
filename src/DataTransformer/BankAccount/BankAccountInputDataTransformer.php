<?php
declare(strict_types=1);

namespace App\DataTransformer\BankAccount;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Entity\BankAccount;

class BankAccountInputDataTransformer implements DataTransformerInterface
{
     public function transform($object, string $to, array $context = [])
    {
        $account = new BankAccount();
        $account->setBankName($object->bankName);
        $account->setIban($object->iban);
        $account->setBankAccountHolderName($object->bankAccountHolderName);

        return $account;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof BankAccount) {
            return false;
        }

        return BankAccount::class === $to && null !== ($context['input']['class'] ?? null);
    }
}