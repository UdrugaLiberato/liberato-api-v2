<?php

namespace App\DataTransformer\Invoice;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\Invoice\InvoiceOutput;
use App\Entity\Invoice;

class InvoiceOutputDataTransformer implements DataTransformerInterface
{
    /**
     * @param object $object
     * @param string $to
     * @param array<mixed> $context
     * @return InvoiceOutput
     */
    public function transform($object, string $to, array $context = []): InvoiceOutput
    {
        return new InvoiceOutput(
            $object->getDescription(),
            $object->getAmount(),
            $object->getPayedAt()->format('Y-m-d H:i:s'),
            $object->getFiles(),
            $object->getProject()
        );
    }

    /**
     * @param object $data
     * @param string $to
     * @param array<mixed> $context
     * @return bool
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return InvoiceOutput::class === $to && $data instanceof Invoice;
    }
}