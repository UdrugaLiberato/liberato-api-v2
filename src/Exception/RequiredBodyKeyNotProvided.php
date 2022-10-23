<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;

class RequiredBodyKeyNotProvided extends Exception
{
    public function __construct(string $key)
    {
        parent::__construct(sprintf('The key "%s" is required in the request body', $key), 422);
    }
}
