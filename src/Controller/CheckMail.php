<?php

namespace App\Controller;

use App\Entity\Emails;
use App\Repository\EmailsRepository;
use SecIT\ImapBundle\Service\Imap;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class CheckMail extends AbstractController {
  public const BACKEND_URL_FILES = 'https://dev.udruga-liberato.hr/uploads/emails_attachments/';

  private string $uploadDir;

  public function __construct(private readonly KernelInterface  $kernel,
                              private readonly EmailsRepository $emailsRepository) {
    $this->uploadDir = $this->kernel->getProjectDir() . '/public/uploads/emails_attachments/';
  }

  #[Route('/check-mail', name: 'check')]
  public function index(Imap $imap) {
    $connection = $imap->get('liberato_imap');

    $mail_ids = $connection->searchMailbox('UNSEEN');
    foreach ($mail_ids as $i) {
      if ($this->emailsRepository->findOneBy(['messageId' => $i])) {
        continue;
      }
      $mail = $connection->getMail($i);
      $attachments = $mail->getAttachments();
      $emailEntity = new Emails();
      $emailEntity->setMessageId($i);
      $emailEntity->setSubject($mail->subject ?? 'No subject');
      $emailEntity->setFromAddress($mail->fromAddress);
      $emailEntity->setFromName($mail->fromName);
      $emailEntity->setBody($this->removeSignatureContent($mail->textHtml) ?? 'No content');
      $emailEntity->setDate($mail->date);
      $attachmentsArray = [];
      foreach ($attachments as $attachment) {
        $filename = time() . '_' . $attachment->name;;
        $filePath = $this->uploadDir . $filename;
        $attachment->setFilePath($filePath);
        $attachment->saveToDisk();
        $attachmentsArray[] = [
            "name" => $attachment->name,
            "filePath" => self::BACKEND_URL_FILES . $filename,
            "size" => $this->formatBytes($attachment->sizeInBytes),
            "mime" => $attachment->mimeType
        ];
      }
      $emailEntity->setAttachments($attachmentsArray);
      $this->emailsRepository->save($emailEntity, true);
    }
    $connection->disconnect();
    return $this->json(['message' => 'Messages checked!']);
  }

  private function formatBytes(?int $bytes): string {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    // Calculate the size and append the unit
    $bytes /= pow(1024, $pow);

    // Format the number with the desired precision
    return round($bytes, 2) . ' ' . $units[$pow];
  }

  function removeSignatureContent($message) {
    $startTag = '<signature';

    $startIndex = strpos($message, $startTag);

    if ($startIndex !== false) {
      $message = substr($message, 0, $startIndex);
    }

    return $message;
  }
}
