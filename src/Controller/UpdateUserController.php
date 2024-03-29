<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\UserRepository;
use App\Utils\LiberatoHelperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[AsController]
class UpdateUserController extends AbstractController {
  public function __construct(
      private UserRepository              $userRepository,
      private LiberatoHelperInterface     $liberatoHelper,
      private UserPasswordHasherInterface $userPasswordEncoder,

  ) {
  }

  public function __invoke(string $id, Request $request): UserInterface {
    $userToUpdate = $this->userRepository->find($id);
    if ($request->files->get('file')) {
      $userToUpdate->getAvatar()->map(function (string $imagePath):
      void {
        if ('anonymous-user.png' === $imagePath) {
          return;
        }
        $file = $this->liberatoHelper->getImagePath('avatar/') . $imagePath;
        if (file_exists($file)) {
          unlink($file);
        }
      });
      $avatar = $this->liberatoHelper->transformImage($request->files->get('file'), 'avatar');
      $userToUpdate->setAvatar($avatar);
    }
    if ($request->get('username') && $request->get('username') !== $userToUpdate->getName()) {
      $userToUpdate->setUsername($request->get('username'));
    }
    if ($request->get('password') !== '' && NULL !== $request->get('password')) {
      $userToUpdate->setPassword(
          $this->userPasswordEncoder->hashPassword($userToUpdate,
              $request->get('password'))
      );
      $userToUpdate->eraseCredentials();
    }

    if ($request->get('email') && $request->get('email') !== $userToUpdate->getEmail()) {
      $userToUpdate->setEmail($request->get('email'));
    }

    if ($request->get('phone') && $request->get('phone') !== $userToUpdate->getPhone()) {
      $userToUpdate->setPhone($request->get('phone'));
    }
    if ($request->get('role') && $request->get('role') !== $userToUpdate->getRoles()[0]) {
      $userToUpdate->setRoles($request->get('role'));
    }

    $userToUpdate->setUpdatedAt(new \DateTimeImmutable('now'));
    return $userToUpdate;
  }
}
