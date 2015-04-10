<?php

namespace App\AppBundle\Command;

use App\AppBundle\Entity\Role;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RolesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('roles:create')
            ->setDescription('Creating the Role')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'What name will have new role'
            )
            ->addArgument(
                'role',
                InputArgument::REQUIRED,
                'ROLE_'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $role = new Role();
        $role->setName($input->getArgument('name'));
        $role->setRole($input->getArgument('role'));

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->persist($role);
        $em->flush();

        $output->writeln('success');
    }
}