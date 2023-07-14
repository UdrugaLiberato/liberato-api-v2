<?php

namespace App\DTO\Volunteer;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class VolunteerInput {
  public string $firstName;
  public string $lastName;
  public string $city;
  public string $email;
  public string $member;
  public string $reason;
  public $resume;
  public \DateTimeImmutable $createdAt;

  public function __construct() {
    $this->resume = $this->resume ?? [];
  }
}