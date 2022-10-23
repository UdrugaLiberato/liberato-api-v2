<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Utils\LiberatoHelperInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserProcessor implements ProcessorInterface
{
    public function __construct(
        private UserRepository          $userRepository,
        private UserPasswordHasherInterface $userPasswordEncoder,
        private LiberatoHelperInterface $liberatoHelper,
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array
    $context = []): User
    {
        $user = new User();
        if ($data->password) {
            $user->setPassword(
                $this->userPasswordEncoder->hashPassword($user, $data->password)
            );
            $user->eraseCredentials();
        }
        $avatar = null === $data->file ? $this->createAnonymousAvatar() :
            $this->liberatoHelper->transformImage(
                $data->file,
                'avatar'
            );
        $user->setUsername($data->username);
        $user->setEmail($data->email);
        $user->setPhone($data->phone ?? null);
        $user->setAvatar($avatar);

        $this->userRepository->add($user);

        return $user;
    }

    private function createAnonymousAvatar(): ArrayCollection
    {
        return new ArrayCollection([
            'src' => 'https://dev.udruga-liberato.hr/anonymous-user.png',
            'title' => 'anonymous-user',
            'path' => 'anonymous-user.png',
            'mime' => 'image/png',
        ]);
    }
}
