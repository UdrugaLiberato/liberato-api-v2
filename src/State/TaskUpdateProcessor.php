<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Task;
use App\Repository\TaskRepository;

class TaskUpdateProcessor implements ProcessorInterface {
  public function __construct(
      private TaskRepository $taskRepository
  ) {
  }

  /**
   * @throws \Exception
   */
  public function process(mixed $data, Operation $operation, array
                                $uriVariables = [], array $context = []): Task {
    $taskToUpdate = $this->taskRepository->find($uriVariables['id']);

    $taskToUpdate->setName($data->name);
    $taskToUpdate->setNote($data->note);
    $taskToUpdate->setIsFinished($data->isFinished === 'true');
    $taskToUpdate->setPriority($data->priority);
    $taskToUpdate->setDeadline(new \DateTimeImmutable($data->deadline));
    if ($data->isFinished) {
      $taskToUpdate->setFinishedAt(new \DateTimeImmutable());
    }

    $this->taskRepository->save($taskToUpdate);

    return $taskToUpdate;
  }
}