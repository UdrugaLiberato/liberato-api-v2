<?php

namespace App\DataTransformer\User;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\Exception\ValidationException;
use App\Entity\User;
use App\Utils\LiberatoHelper;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserInputDataTransformer implements DataTransformerInterface
{
    public function __construct(private ValidatorInterface $validator, private KernelInterface $kernel)
    {
    }

    public function transform($object, string $to, array $context = []): object
    {
        $user = new User();
        if ($object->file) {
            $errors = $this->validator->validate($object->file, new Image());
            if (count($errors) > 0) {
                throw new ValidationException("Only images can be uploaded!");
            }

            $originalFilename = pathinfo(
                $object->file->getClientOriginalName(),
                PATHINFO_FILENAME
            );
            // this is needed to safely include the file name as part of the URL
            $safeFilename = LiberatoHelper::slugify($originalFilename);
            $newFilename = date('Y-m-d') . "_" . $safeFilename . md5
                (
                    microtime()
                ) . '.'
                . $object->file->guessExtension();

            $object->file->move($this->kernel->getProjectDir() . '/public/images/avatar/', $newFilename);
            $user->setFile($object->file);
            $user->setFilePath('/images/avatar/' . $newFilename);
        }
        $user->setUsername($object->username);
        $user->setPassword($object->password);
        $user->setEmail($object->email);
        $user->setPhone($object->phone);
        $user->setPhone($object->phone);

        return $user;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof User) {
            return false;
        }

        return User::class === $to && null !== ($context['input']['class'] ?? null);
    }
}