<?php

namespace App\DTO\Task;

class TaskUpdateInput {
  public string $name;
  public string $note;
  public string $isFinished;
  public string $priority;
public string $deadline;
}