<?php

declare(strict_types=1);

namespace App\State\Extension;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\ArrayPaginator;

interface PaginationExtensionInterface
{
    public function getResult(array $collection, string $resourceClass, ?Operation $operation =
    null, array $context = []): ArrayPaginator;

    public function isEnabled(string $resourceClass = null, ?Operation $operation = null, array $context = []): bool;
}
