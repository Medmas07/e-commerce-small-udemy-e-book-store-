<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Créer un utilisateur administrateur.',
)]
class CreateAdminCommand extends Command
{
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $hasher;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $hasher)
    {
        parent::__construct();
        $this->em = $em;
        $this->hasher = $hasher;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var \Symfony\Component\Console\Helper\QuestionHelper $helper */
        $helper = $this->getHelper('question');

        $emailQuestion = new Question('Email de l\'admin: ');
        $email = $helper->ask($input, $output, $emailQuestion);

        $passwordQuestion = new Question('Mot de passe: ');
        $passwordQuestion->setHidden(true);
        $plainPassword = $helper->ask($input, $output, $passwordQuestion);

        $firstNameQuestion = new Question('Prénom : ');
        $firstName = $helper->ask($input, $output, $firstNameQuestion);

        $user = new User();
        $user->setEmail($email);
        $user->setFirstName($firstName);
        $user->setRoles(['ROLE_ADMIN']); // ROLE_USER sera automatiquement ajouté
        $user->setPassword($this->hasher->hashPassword($user, $plainPassword));

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln('<info>Admin créé avec succès !</info>');

        return Command::SUCCESS;
    }
}
