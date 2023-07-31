<?php

namespace App\DTO\Task;

class TaskInput {
  public string $name;
  public string $priority;
  public ?string $note = null;
  public string $assignedTo;
  public \DateTimeImmutable $deadline;
}