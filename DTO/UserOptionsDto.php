<?php

declare(strict_types=1);

namespace Appyfurious\AdminUserBundle\DTO;

class UserOptionsDto
{
    public string $username;
    public string $emailAddress;
    public string $confirmationUrl;

    public function __construct(string $username, string $emailAddress, string $confirmationUrl)
    {
        $this->username = $username;
        $this->emailAddress = $emailAddress;
        $this->confirmationUrl = $confirmationUrl;
    }
}