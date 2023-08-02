<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TaskPostProcessor implements ProcessorInterface {
  public function __construct(
      private TaskRepository          $taskRepository,
      private readonly UserRepository $userRepository,
      private MailerInterface         $mailer,
      private TokenStorageInterface   $token
  ) {
  }

  public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Task {
    $deadline = $data->deadline;
    $user = $this->token->getToken()->getUser();
    if (!$user instanceof User) {
      throw new \InvalidArgumentException('User must be logged in.');
    }
    $currentDate = new \DateTimeImmutable();
    $assignee = $this->userRepository->find($data->assignedTo);
    if (!$assignee) {
      throw new \InvalidArgumentException('Assignee does not exist.');
    }

    $allowedPriorities = ["low", "medium", "high", "urgent"];
    if (!in_array($data->priority, $allowedPriorities)) {
      throw new \InvalidArgumentException('Priority must be one of: ' . implode(', ', $allowedPriorities));
    }

    if ($deadline < $currentDate) {
      throw new \InvalidArgumentException('Deadline cannot be in the past.');
    }

    $task = new Task();
    $task->setCreatedBy($user);
    $task->setName($data->name);
    $task->setPriority($data->priority);
    $task->setNote($data->note);
    $task->setDeadline($data->deadline);
    $task->setAssignedTo($assignee);
    $this->taskRepository->add($task);
    $this->sendEmail($assignee, $task);
    return $task;
  }

  private function sendEmail(User $assignee, Task $data): void {
    $sendEmail = (new Email())
        ->from(new Address('stipo@udruga-liberato.hr', 'Liberato'))
        ->to($assignee->getEmail())
        ->priority(Email::PRIORITY_HIGH)
        ->subject('Imate novi zadatak!')
        ->html('<p>Zadatak: ' . $data->getName() . '</p><p>Prioritet: ' .
            $data->getPriority() . '</p><p>Note: ' . $data->getNote() . '</p><p>Rok: 
            ' . $data->getDeadline()->format('Y-m-d H:i:s') . '</p> <br /> <p>Ovaj zadatak je kreiran ' . $data->getCreatedAt()->format(' Y-m-d H:i:s') . '
            Želite li vidjeti putem preglednika, kliknite <a href="http://udruga-liberato.hr/admin/tasks/' . $data->getId() . '">ovdje</a>.
            </p> <br /> <br /> <p>
            Ovaj email je automatski generiran. Molimo ne odgovarajte na njega.</p>');

    $this->mailer->send($sendEmail);
  }
}
