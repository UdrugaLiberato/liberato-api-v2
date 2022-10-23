<?php

declare(strict_types=1);

namespace App\DataTransformer\Invoice;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\Invoice\InvoiceOutput;
use App\Entity\Invoice;

class InvoiceOutputDataTransformer implements DataTransformerInterface
{
    /**
     * @param Invoice $object
     * @param array<mixed> $context
     */
    public function transform($object, string $to, array $context = []): InvoiceOutput
    {
        return new InvoiceOutput(
            $object->getId(),
            $object->getDescription(),
            $object->getAmount(),
            $object->getCurrency(),
            $object->getInvoiceNumber(),
            $object->isSendToAccountant(),
            $object->getPayedAt()->format('Y-m-d H:i:s'),
            $object->getFiles(),
            $object->getProject(),
            $object->getCreatedAt()->format('Y-m-d H:i:s'),
            $object->getUpdatedAt()?->format('Y-m-d H:i:s') ?? null
        );
    }

    /**
     * @param object $data
     * @param array<mixed> $context
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return InvoiceOutput::class === $to && $data instanceof Invoice;
    }
}
