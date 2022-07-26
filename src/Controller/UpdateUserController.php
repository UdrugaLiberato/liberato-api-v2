<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Utils\LiberatoHelperInterface;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Core\User\UserInterface;

#[AsController]
class UpdateUserController extends AbstractController
{
    public function __construct(
        private UserRepository          $userRepository,
        private LiberatoHelperInterface $liberatoHelper
    )
    {
    }

    public function __invoke(string $id, Request $request): UserInterface
    {
        $oldUser = $this->userRepository->find($id);
        $avatar = $this->$this->liberatoHelper->transformImage($request->files->get('file'), "avatar");

        if ($request->get("username") && $request->get("username") !== $oldUser->getName()) {
            $oldUser->setUsername($request->get("username"));
        }

        if ($request->get("password")) {
            $oldUser->setPassword($request->get("password"));
        }

        if ($request->get("email") && $request->get("email") !== $oldUser->getEmail()) {
            $oldUser->setEmail($request->get("email"));
        }

        if ($request->get("phone") && $request->get("phone") !== $oldUser->getPhone()) {
            $oldUser->setPhone($request->get("phone"));
        }
        if ($request->get("role") && $request->get("role") !== $oldUser->getRoles()[0]) {
            $oldUser->setRoles($request->get("role"));
        }

        $oldUser->setAvatar($avatar);
        $oldUser->setUpdatedAt(new DateTimeImmutable("now"));
        $this->userRepository->update($oldUser);

        return $oldUser;
    }
}