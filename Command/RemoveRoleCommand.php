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

class RemoveRoleCommand extends Command
{
    protected static $defaultName = 'remove-role';

    private EntityManager $em;

    public function __construct(EntityManager $em, UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct(self::$defaultName);
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputArgument('username', InputArgument::REQUIRED, 'The username'),
                new InputArgument('role', InputArgument::REQUIRED, 'The role to remove')
            ));
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $username = $input->getArgument('username');
        $role = $input->getArgument('role');

        $user = $this->em->getRepository(AdminUser::class)->findOneBy(['username' => $username]);

        if (!$user) {
            throw new \InvalidArgumentException(sprintf('User identified by "%s" username does not exist.', $username));
        }

        /** @var AdminUser */
        $user->removeRole($role);

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln('Role ' . $role . ' was removed from ' . $username);

        return 0;
    }
}