<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Exception\EmailCouldNotBeCreated;
use App\Repository\UserRepository;
use App\Utils\LiberatoHelperInterface;
use Doctrine\Common\Collections\ArrayCollection;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserProcessor implements ProcessorInterface {
  public function __construct(
      private UserRepository              $userRepository,
      private UserPasswordHasherInterface $userPasswordEncoder,
      private LiberatoHelperInterface     $liberatoHelper,
  ) {
  }

  public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): User {
    $user = new User();
    if ($data->password) {
      $user->setPassword(
          $this->userPasswordEncoder->hashPassword($user, $data->password)
      );
      $user->eraseCredentials();
    }
    if ($data->createEmail === "true") {
      $status = $this->liberatoHelper->createEmail($data->username,
          $data->email,
          $data->password);

      if ($status !== 200) {
        throw new EmailCouldNotBeCreated("Email could not be created.", 500);
      }
    }
    $avatar = NULL === $data->file ? $this->createAnonymousAvatar() :
        $this->liberatoHelper->transformImage(
            $data->file,
            'avatar'
        );
    $user->setUsername($data->username);
    $user->setEmail($data->email);
    $user->setPhone($data->phone ?? NULL);
    $user->setAvatar($avatar);
    $user->setRoles($data->role);

    $this->userRepository->add($user);

    return $user;
  }

  #[Pure]
  private function createAnonymousAvatar(): ArrayCollection {
    return new ArrayCollection([
        'src' => 'https://dev.udruga-liberato.hr/anonymous-user.png',
        'title' => 'anonymous-user',
        'path' => 'anonymous-user.png',
        'mime' => 'image/png',
    ]);
  }
}
