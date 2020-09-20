<?php

declare(strict_types=1);

namespace Appyfurious\AdminUserBundle\Entity;

use DateTime;
use Ramsey\Uuid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * AppyfuriousAdminUserBundle\Entity\AdminUser
 *
 * @ORM\Entity(repositoryClass="Appyfurious\AdminUserBundle\Repository\AdminUserRepository")
 * @ORM\Table(name="user")
 */
class AdminUser implements UserInterface
{
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    /**
     * @ORM\Id
     * @ORM\Column(type="string", unique=true, nullable=false, length=255)
     */
    private string $id;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    private string $username;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    private string $email;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    private string $password;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private DateTime $created;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private array $roles;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $enabled;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $active;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?DateTime $lastLogin;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private string $token;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private string $confirmationToken;


    public function __construct()
    {
        $this->id = Uuid::uuid4()->toString();
        $this->created = new DateTime('now', new \DateTimeZone('UTC'));
        $this->enabled = false;
        $this->roles = [];
        $this->active = false;
    }

    public function __toString(): string
    {
        return $this->username ?: 'New AdminUser';
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_ADMIN
        $roles[] = self::ROLE_ADMIN;

        return array_unique($roles);
    }

    public function hasRole(string $role): bool
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    public function setRoles(array $roles): void
    {
        $this->roles = array();

        foreach ($roles as $role) {
            $this->addRole($role);
        }
    }

    public function addRole(?string $role): void
    {
        $role ? null : $role = self::ROLE_ADMIN;
        $role = strtoupper($role);

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(self::ROLE_SUPER_ADMIN);
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function eraseCredentials(): void
    {
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $boolean): void
    {
        $this->enabled = $boolean;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $boolean): void
    {
        $this->active = $boolean;
    }

    public function getLastLogin(): ?DateTime
    {
        return $this->lastLogin;
    }

    public function setLastLogin(DateTime $time = null): void
    {
        $this->lastLogin = $time;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): void
    {
        if ($token !== null) {
            $this->token = $token;
        }
    }

    public function getConfirmationToken(): string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(string $confirmationToken): void
    {
        $this->confirmationToken = $confirmationToken;
    }
}