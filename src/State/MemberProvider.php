<?php

namespace App\State;

use ApiPlatform\Exception\RuntimeException;
use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\MemberRepository;
use Exception;

class MemberProvider implements ProviderInterface {
  public function __construct(private readonly MemberRepository $memberRepository) {
  }

  public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null {
    if ($operation instanceof CollectionOperationInterface) {
      try {
        return $this->memberRepository->findAll();
      } catch (Exception $exception) {
        throw new RuntimeException(sprintf('Unable to member volunteer from external source: %s', $exception->getMessage()));
      }
    }
    return $this->memberRepository->find($uriVariables['id']);
  }
}
