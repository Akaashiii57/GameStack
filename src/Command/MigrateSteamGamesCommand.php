<?php

namespace App\Command;

use App\Entity\Game;
use App\Entity\LibraryGame;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:migrate-steam-games',
    description: 'Migre les jeux Steam existants vers les nouvelles conventions',
)]
class MigrateSteamGamesCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $io->title('Migration des jeux Steam');
        
        // 1. Corriger les statuts "possédé" vers "À faire"
        $libraryGames = $this->entityManager->getRepository(LibraryGame::class)
            ->createQueryBuilder('lg')
            ->join('lg.game', 'g')
            ->where('lg.status = :oldStatus')
            ->andWhere('g.mode = :steamMode')
            ->setParameter('oldStatus', 'possédé')
            ->setParameter('steamMode', 'steam')
            ->getQuery()
            ->getResult();
        
        $io->writeln(sprintf('Found %d LibraryGame avec statut "possédé"', count($libraryGames)));
        
        foreach ($libraryGames as $lg) {
            $lg->setStatus('À faire');
            $this->entityManager->persist($lg);
        }
        
        $this->entityManager->flush();
        $io->success(sprintf('Statuts migrés: %d jeux', count($libraryGames)));
        
        // 2. Mettre à jour les couvertures Steam
        $games = $this->entityManager->getRepository(Game::class)
            ->createQueryBuilder('g')
            ->where('g.mode = :steamMode')
            ->andWhere('g.cover IS NULL')
            ->setParameter('steamMode', 'steam')
            ->getQuery()
            ->getResult();
        
        $io->writeln(sprintf('Found %d jeux Steam sans couverture', count($games)));
        
        // Note: On ne peut pas générer l'URL sans appId, donc on skip cette partie
        $io->note('Les URLs de couverture nécessitent appId, impossible à générer automatiquement');
        
        $io->success('Migration terminée');
        
        return Command::SUCCESS;
    }
}