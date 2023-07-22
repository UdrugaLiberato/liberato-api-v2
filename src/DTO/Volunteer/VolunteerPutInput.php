<?php

namespace App\DTO\Volunteer;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class VolunteerPutInput {
  public string $notes;
  public $newResume;

}