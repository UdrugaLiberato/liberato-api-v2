<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Volunteer;
use App\Repository\VolunteerRepository;
use App\Utils\LiberatoHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;

class VolunteerProcessor implements ProcessorInterface {
  public const BACKEND_URL_RESUMES = 'https://dev.udruga-liberato.hr/resumes/';
  public string $uploadDir;

  public function __construct(
      private readonly VolunteerRepository $volunteerRepository,
      private readonly KernelInterface     $kernel
  ) {
    $this->uploadDir = $this->kernel->getProjectDir() . '/public/resumes/';
  }

  public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Volunteer {
    // get resume if exists and save it to variable
    $resume = $data->resume;
    $volunteer = new Volunteer();
    if (empty($resume) === false) {
      $file = $this->uploadFile($resume);
      $volunteer->setResume($file);
    }

    $volunteer->setFirstName($data->firstName);
    $volunteer->setLastName($data->lastName);
    $volunteer->setEmail($data->email);
    $volunteer->setCity($data->city);
    $volunteer->setMembership($data->member === 'true');
    $volunteer->setReason($data->reason);
    $this->volunteerRepository->save($volunteer, true);

    return $volunteer;
  }

  private function uploadFile(UploadedFile $resume): array {

    $ext = $resume->getClientOriginalExtension();
    $mime = $resume->getMimeType();
    $size = $resume->getSize();
    $originalFilename = pathinfo(
        $resume->getClientOriginalName(),
        PATHINFO_FILENAME
    );
    // this is needed to safely include the file name as part of the URL
    $safeFilename = LiberatoHelper::slugify($originalFilename);
    $newFilename = date('Y-m-d') . '_' . $safeFilename . '.'
        . $ext;

    $resume->move(
        $this->uploadDir, $newFilename
    );

//    dd($resume);

    $file = [
        'name' => $newFilename,
        'ext' => $ext,
        'size' => $this->formatBytes($size),
        'mime' => $mime,
        'url' => self::BACKEND_URL_RESUMES . $newFilename
    ];

    return $file;
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
}
