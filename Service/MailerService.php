<?php

declare(strict_types=1);

namespace Appyfurious\AdminUserBundle\Service;

use Appyfurious\AdminUserBundle\DTO\UserOptionsDto;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment;

class MailerService
{
    private Swift_Mailer $mailer;
    private string $fromEmail;
    private Environment $twig;

    public function __construct(Swift_Mailer $mailer, string $fromEmail, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->fromEmail = $fromEmail;
        $this->twig = $twig;
    }

    public function sendWelcomeEmail(string $host, UserOptionsDto $userOptions): void
    {
        $message = (new Swift_Message('Welcome ' . $userOptions->username))
            ->setFrom($this->fromEmail)
            ->setTo($userOptions->emailAddress)
            ->setBody(
                $this->twig->render(
                    '@AppyfuriousAdminUser/AdminUser/invite_email.html.twig',
                    [
                        'username' => $userOptions->username,
                        'host' => $host,
                        'confirmationUrl' => $userOptions->confirmationUrl
                    ]
                ),
                'text/html'
            );

        $this->mailer->send($message);
    }

    public function sendResetEmail(UserOptionsDto $userOptions): void
    {
        $message = (new Swift_Message('Reset Password'))
            ->setFrom($this->fromEmail)
            ->setTo($userOptions->emailAddress)
            ->setBody(
                $this->twig->render(
                    '@AppyfuriousAdminUser/Resetting/reset_pass.html.twig',
                    [
                        'username' => $userOptions->username,
                        'confirmationUrl' => $userOptions->confirmationUrl
                    ]
                ),
                'text/html'
            );

        $this->mailer->send($message);
    }
}