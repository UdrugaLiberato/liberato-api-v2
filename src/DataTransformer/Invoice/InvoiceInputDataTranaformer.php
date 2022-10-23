<?php

declare(strict_types=1);

namespace App\DataTransformer\Invoice;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\Invoice\InvoiceInput;
use App\Entity\Invoice;
use App\Repository\ProjectRepository;
use App\Utils\LiberatoHelperInterface;
use DateTimeImmutable;
use Exception;

class InvoiceInputDataTranaformer implements DataTransformerInterface
{
    public function __construct(
        private LiberatoHelperInterface $liberatoHelper,
        private ProjectRepository       $projectRepository
    )
    {
    }

    /**
     * @param InvoiceInput $object
     * @param array<mixed> $context
     *
     * @throws Exception
     */
    public function transform($object, string $to, array $context = []): Invoice
    {
        $project = $this->projectRepository->find($object->project);

        $invoice = new Invoice();
        $invoice->setDescription($object->description);
        $invoice->setAmount($object->amount);
        $invoice->setCurrency($object->currency);
        $invoice->setInvoiceNumber($object->invoiceNumber);
        $invoice->setPayedAt(new DateTimeImmutable($object->payedAt));
        $invoice->setProject($project);
        $fileNames = $this->liberatoHelper->transformFiles($object->files, 'invoices/');
        $invoice->setFiles($fileNames);

        return $invoice;
    }

    /**
     * @param object $data
     * @param array<mixed> $context
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Invoice) {
            return false;
        }

        return Invoice::class === $to && null !== ($context['input']['class'] ?? null);
    }
}
