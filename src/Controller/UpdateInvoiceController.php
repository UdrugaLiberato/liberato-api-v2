<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Invoice;
use App\Repository\InvoiceRepository;
use App\Utils\LiberatoHelperInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class UpdateInvoiceController
{
    public function __construct(
        private InvoiceRepository $invoiceRepository,
        private LiberatoHelperInterface $liberatoHelper
    ) {
    }

    public function __invoke(string $id, Request $request): Invoice
    {
        return $this->invoiceRepository->find($id);
    }
}
