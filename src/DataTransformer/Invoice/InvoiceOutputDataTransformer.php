<?php

namespace App\DataTransformer\Invoice;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\Invoice\InvoiceOutput;
use App\Entity\Invoice;

class InvoiceOutputDataTransformer implements DataTransformerInterface
{
    public function transform($object, string $to, array $context = [])
    {
        return new InvoiceOutput(
            $object->getDescription(),
            $object->getAmount(),
            $object->getPayedAt()->format('Y-m-d H:i:s'),
            $object->getFiles(),
            $object->getProject()
        );
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return InvoiceOutput::class === $to && $data instanceof Invoice;
    }
}