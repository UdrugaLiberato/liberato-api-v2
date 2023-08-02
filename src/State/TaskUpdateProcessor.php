<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Task;
use App\Repository\TaskRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class TaskUpdateProcessor implements ProcessorInterface {
  public function __construct(
      private TaskRepository $taskRepository,
      private MailerInterface $mailer
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
      $this->notifyCreatorOfTaskFinish($taskToUpdate);
    }

    $this->taskRepository->save($taskToUpdate);

    return $taskToUpdate;
  }

  private function notifyCreatorOfTaskFinish(Task $task): void {
    $sendEmail = (new Email())
        ->from(new Address('stipo@udruga-liberato.hr', 'Liberato'))
      ->to($task->getCreatedBy()->getEmail())
        ->priority(Email::PRIORITY_HIGH)
        ->subject('Zadatak napravljen!')
        ->html('<p>Zadatak: ' . $task->getName() . '</p><p>Prioritet: ' .
            $task->getPriority() . '</p><p>Note: ' . $task->getNote() . '</p><p>Napravljen: 
            ' . $task->getFinishedAt()->format('Y-m-d H:i:s') . '</p> <br /> <p>Ovaj zadatak je obavljen. Korisnik ' . $task->getAssignedTo()->getUsername() . '
            Želite li vidjeti putem preglednika, kliknite <a href="http://udruga-liberato.hr/admin/tasks/' . $task->getId() . '">ovdje</a>.
            </p> <br /> <br /> <p>
            Ovaj email je automatski generiran. Molimo ne odgovarajte na njega.</p>');

    $this->mailer->send($sendEmail);
  }
}