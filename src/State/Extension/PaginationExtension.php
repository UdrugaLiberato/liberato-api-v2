<?php

declare(strict_types=1);

namespace App\State\Extension;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\ArrayPaginator;
use ApiPlatform\State\Pagination\Pagination;

class PaginationExtension implements PaginationExtensionInterface {
  public function __construct(private Pagination $pagination) {
  }

  public function getResult(array $collection, string $resourceClass, ?Operation $operation =
  NULL, array                     $context = []): ArrayPaginator {
    [, $offset, $itemPerPage] = $this->pagination->getPagination($operation, $context);

    return new ArrayPaginator($collection, $offset, $itemPerPage);
  }

  public function isEnabled(string $resourceClass = NULL, ?Operation $operation = NULL, array $context = []): bool {
    return $this->pagination->isEnabled($operation, $context);
  }
}
