<?php

namespace App\Controller;

use ApiPlatform\Core\Validator\Exception\ValidationException;
use App\Repository\UserRepository;
use App\Utils\LiberatoHelper;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsController]
class UpdateUserController extends AbstractController
{
    public function __construct(
        private UserRepository     $userRepository,
        private ValidatorInterface $validator,
        private KernelInterface    $kernel
    )
    {
    }

    public function __invoke(string $id, Request $request)
    {
        $oldUser = $this->userRepository->find($id);
        $avatar = $request->files->get("file");
        if ($avatar !== null) {
            $errors = $this->validator->validate($avatar, new Image());
            if (count($errors) > 0) {
                throw new ValidationException("Only images can be uploaded!");
            }

            $originalFilename = pathinfo(
                $avatar->getClientOriginalName(),
                PATHINFO_FILENAME
            );
            // this is needed to safely include the file name as part of the URL
            $safeFilename = LiberatoHelper::slugify($originalFilename);
            $newFilename = date('Y-m-d') . "_" . $safeFilename . md5
                (
                    microtime()
                ) . '.'
                . $avatar->guessExtension();

            $avatar->move($this->kernel->getProjectDir() . '/public/images/avatar/', $newFilename);
            $oldUser->setFilePath('/images/avatar/' . $newFilename);
        }

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

        $oldUser->setUpdatedAt(new DateTimeImmutable("now"));
        $this->userRepository->update($oldUser);

        return $oldUser;
    }
}