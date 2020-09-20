<?php

declare(strict_types=1);

namespace Appyfurious\AdminUserBundle\Repository;

use Appyfurious\AdminUserBundle\Entity\AdminUser;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

class AdminUserRepository extends EntityRepository implements UserLoaderInterface
{
    public function loadUserByUsername(string $username): object
    {
        return $this->findOneBy(['username' => $username]);
    }

    public function findUserByUsernameOrEmail(string $usernameOrEmail): AdminUser
    {
        if (preg_match('/^.+@\S+\.\S+$/', $usernameOrEmail)) {
            $user = $this->findOneBy(['email' => $usernameOrEmail]);
            if (null !== $user) {
                return $user;
            }
        }

        return $this->findOneBy(['username' => $usernameOrEmail]);
    }

    public function findUserByConfirmationToken(string $token): AdminUser
    {
        return $this->findOneBy(['confirmationToken' => $token]);
    }

    public function findUserByActiveStatus(): array
    {
        return $this->findBy(['active' => 1]);
    }

    public function findUserByToken(string $token): AdminUser
    {
        return $this->findOneBy(['token' => $token]);
    }
}