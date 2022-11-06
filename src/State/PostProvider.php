<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\PostRepository;

class PostProvider implements ProviderInterface
{
    public function __construct(
        private PostRepository $repository,
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return $this->repository->findOneBy(["slug" => $uriVariables["id"]]);
    }
}
