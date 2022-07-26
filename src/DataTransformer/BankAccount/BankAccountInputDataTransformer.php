<?php
declare(strict_types=1);

namespace App\DataTransformer\BankAccount;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Entity\BankAccount;

class BankAccountInputDataTransformer implements DataTransformerInterface
{
    /**
     * @param object $object
     * @param string $to
     * @param array<mixed> $context
     * @return BankAccount
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
     * @param object $data
     * @param string $to
     * @param array<mixed> $context
     * @return bool
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof BankAccount) {
            return false;
        }

        return BankAccount::class === $to && null !== ($context['input']['class'] ?? null);
    }
}