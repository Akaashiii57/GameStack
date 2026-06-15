<?php

namespace App\Command;

use App\Entity\SteamAccount;
use App\Service\SteamAuthService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:force-sync-marvel-rivals',
    description: 'Force la synchronisation de Marvel Rivals avec les données complètes',
)]
class ForceSyncMarvelRivalsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SteamAuthService $steamAuthService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $io->title('Force sync Marvel Rivals');
        
        // Trouver le premier compte Steam (pour tester)
        $steamAccount = $this->entityManager->getRepository(SteamAccount::class)
            ->findOneBy([]);
        
        if (!$steamAccount) {
            $io->error('Aucun compte Steam trouvé');
            return Command::FAILURE;
        }
        
        $user = $steamAccount->getUser();
        $steamId = $steamAccount->getSteamId();
        
        $io->writeln(sprintf('Utilisateur: %s', $user->getUsername()));
        $io->writeln(sprintf('SteamID: %s', $steamId));
        
        $success = $this->steamAuthService->forceSyncGame($user, $steamId, 'Marvel Rivals');
        
        if ($success) {
            $io->success('Marvel Rivals synchronisé avec succès !');
        } else {
            $io->error('Marvel Rivals non trouvé dans la bibliothèque Steam');
        }

        return $success ? Command::SUCCESS : Command::FAILURE;
    }
}