<?php

namespace App\DataTransformer\User;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Entity\User;
use Symfony\Component\HttpKernel\KernelInterface;

class UserInputDataTransformer implements DataTransformerInterface
{
    public function __construct(private KernelInterface $kernel)
    {
    }

    public function transform($object, string $to, array $context = []): object
    {

        $originalFilename = pathinfo(
            $object->file->getClientOriginalName(),
            PATHINFO_FILENAME
        );
        // this is needed to safely include the file name as part of the URL
        $safeFilename = $this->slugify($originalFilename);
        $newFilename = date('Y-m-d') . "_" . $safeFilename . md5
            (
                microtime()
            ) . '.'
            . $object->file->guessExtension();

        $object->file->move($this->kernel->getProjectDir() . '/public/media/avatar/', $newFilename);
        $user = new User();
        $user->setUsername($object->username);
        $user->setPassword($object->password);
        $user->setEmail($object->email);
        $user->setPhone($object->phone);
        $user->setPhone($object->phone);
        $user->setFile($object->file);
        $user->setFilePath('/media/avatar/' . $newFilename);
        if($object->role){
            $user->setRoles($object->role);
        } else {
            $user->setRoles(User::ROLE_USER);
        }
        return $user;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof User) {
            return false;
        }

        return User::class === $to && null !== ($context['input']['class'] ?? null);
    }

    private function slugify(string $title): string
    {
        $title = preg_replace('~[^\pL\d]+~u', '-', $title);
        $title = iconv('utf-8', 'us-ascii//TRANSLIT', $title);
        $title = preg_replace('~[^-\w]+~', '', $title);
        $title = trim($title, '-');
        $title = preg_replace('~-+~', '-', $title);

        return strtolower($title);
    }
}