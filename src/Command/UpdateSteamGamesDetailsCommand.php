<?php

namespace App\Command;

use App\Entity\Game;
use App\Service\SteamAuthService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update-steam-games-details',
    description: 'Met à jour les jeux Steam existants avec les détails complets via GetAppDetails',
)]
class UpdateSteamGamesDetailsCommand extends Command
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
        
        $io->title('Mise à jour des détails des jeux Steam');
        
        // Récupérer tous les jeux Steam
        $games = $this->entityManager->getRepository(Game::class)
            ->createQueryBuilder('g')
            ->where('g.mode = :steamMode')
            ->setParameter('steamMode', 'steam')
            ->getQuery()
            ->getResult();
        
        $io->writeln(sprintf('Found %d jeux Steam à mettre à jour', count($games)));
        
        $updatedCount = 0;
        
        foreach ($games as $game) {
            // Extraire appId du titre ou autre méthode (pour l'instant on skip)
            // Cette commande nécessiterait une table SteamGame avec appId
            $io->writeln(sprintf('Skipping %s (appId non disponible)', $game->getTitle()));
        }
        
        $io->success(sprintf('Mise à jour terminée (nécessite appId dans la base)'));

        
        return Command::SUCCESS;
    }
}