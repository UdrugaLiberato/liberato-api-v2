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
    $volunteer->setMembership(false);
    $volunteer->setReason($data->reason);
    $this->volunteerRepository->save($volunteer, true);

    return $volunteer;
  }

  private function uploadFile(UploadedFile $resume): ArrayCollection {
    $file = new ArrayCollection();

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

    $file->add($newFilename);
    $file->add($ext);
    $file->add($size);
    $file->add($mime);
    $file->add(self::BACKEND_URL_RESUMES . $newFilename);
    return $file;
  }
}
