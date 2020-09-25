<?php

declare(strict_types=1);

namespace Appyfurious\AdminUserBundle\Command;

use Appyfurious\AdminUserBundle\Entity\AdminUser;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ChangePasswordCommand extends Command
{
    protected static $defaultName = 'change-pass';

    private EntityManager $em;
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(EntityManager $em, UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct(self::$defaultName);
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputArgument('username', InputArgument::REQUIRED, 'The username'),
                new InputArgument('password', InputArgument::REQUIRED, 'The password')
            ));
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        $user = $this->em->getRepository(AdminUser::class)->findOneBy(['username' => $username]);

        if (!$user) {
            throw new \InvalidArgumentException(sprintf('User identified by "%s" username does not exist.', $username));
        }

        $user->setPassword($this->passwordEncoder->encodePassword($user, $password));

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln('Password of user ' . $username . ' was changed.');

        return 0;
    }
}