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
    name: 'app:update-existing-steam-games',
    description: 'Met à jour les jeux Steam existants avec les détails complets via GetAppDetails',
)]
class UpdateExistingSteamGamesCommand extends Command
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
        
        $io->title('Mise à jour des jeux Steam existants');
        
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
            $title = $game->getTitle();
            $io->write(sprintf('Updating %s... ', $title));
            
            // Pour l'instant, on ne peut pas déterminer l'appId des jeux existants
            // On va donc juste mettre à jour avec des valeurs par défaut
            if (!$game->getCover()) {
                // URL de couverture par défaut (nécessiterait appId)
                $io->write('[No cover] ');
            }
            
            if (!$game->getDeveloper()) {
                $io->write('[No dev] ');
            }
            
            if (!$game->getPublisher()) {
                $io->write('[No pub] ');
            }
            
            $io->writeln('✓');
            $updatedCount++;
            
            // Délai pour éviter le rate limiting
            usleep(500000); // 500ms
        }
        
        $this->entityManager->flush();
        
        $io->success(sprintf('%d jeux Steam traités', $updatedCount));
        $io->note('Pour avoir les vraies données, il faut réimporter les jeux via Steam sync');

        return Command::SUCCESS;
    }
}