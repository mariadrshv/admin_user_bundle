<?php

declare(strict_types=1);

namespace Appyfurious\AdminUserBundle\Command;

use Appyfurious\AdminUserBundle\Entity\AdminUser;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateAdminUserCommand extends Command
{
    protected static $defaultName = 'create-adminuser';

    private EntityManager $em;
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(EntityManager $em, UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct(self::$defaultName);
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $helper = $this->getHelper('question');
        $question1 = new Question('Please, choose an username: ');
        $question2 = new Question('Choose an email: ');
        $question3 = new Question('Choose a password: ');
        $question4 = new Question('Choose a role (enter \'admin\' or \'super-admin\'): ');

        $username = $helper->ask($input, $output, $question1);
        $email = $helper->ask($input, $output, $question2);
        $pass = $helper->ask($input, $output, $question3);
        $role = $helper->ask($input, $output, $question4);

        $adminUser = new AdminUser();
        $adminUser->setUsername($username);
        $adminUser->setEmail($email);
        $adminUser->setPassword($this->passwordEncoder->encodePassword($adminUser, $pass));
        $adminUser->setActive(true);
        $adminUser->setEnabled(true);
        $role === 'super-admin' ? $adminUser->setRoles([AdminUser::ROLE_SUPER_ADMIN]) : $adminUser->setRoles([AdminUser::ROLE_ADMIN]);

        $this->em->persist($adminUser);
        $this->em->flush();

        $output->writeln('User ' . $username . ' with email ' . $email .  ' was created.');

        return 0;
    }
}