<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Volunteer;
use App\Repository\VolunteerRepository;
use App\Utils\LiberatoHelper;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;

class VolunteerPutProcessor implements ProcessorInterface {

  public const BACKEND_URL_RESUMES = 'https://dev.udruga-liberato.hr/resumes/';
  public string $uploadDir;
  public string $destinationDir;

  public function __construct(
      private readonly VolunteerRepository $volunteerRepository,
      private readonly KernelInterface     $kernel,
  ) {
    $this->uploadDir = $this->kernel->getProjectDir() . '/public/resumes/';
    $this->destinationDir = $this->kernel->getProjectDir() . '/public/old_resumes/';
  }

  public function process(mixed $data, Operation $operation, array
  $uriVariables = [], array $context = []): Volunteer {
    $volunteer = $this->volunteerRepository->find($uriVariables['id']);
    if ($data->newResume) {
      if ($volunteer->getResume()['name']) {
        $path = $this->kernel->getProjectDir() . '/public/' . substr
            ($volunteer->getResume()['url'], strpos
            ($volunteer->getResume()['url'], 'resumes'));
        if (file_exists($path)) {
          if (!file_exists($this->destinationDir)) {
            mkdir($this->destinationDir, 0777, true);
          }
          $filesystem = new Filesystem();
          $filesystem->rename($path, $this->destinationDir .
              $volunteer->getResume()['name'] . time());
        }
      }
      $volunteer->setResume($this->uploadFile($data->newResume));
    }
      $volunteer->setNotes($data->notes);

    $this->volunteerRepository->save($volunteer, true);

    return $this->volunteerRepository->find($uriVariables['id']);
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


    return [
        'name' => $newFilename,
        'ext' => $ext,
        'size' => $this->formatBytes($size),
        'mime' => $mime,
        'url' => self::BACKEND_URL_RESUMES . $newFilename
    ];
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
