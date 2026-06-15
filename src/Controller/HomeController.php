<?php

namespace App\Controller;

use App\Entity\LibraryGame;
use App\Repository\GameUserRepository;
use App\Repository\SteamGameRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    public function __construct(
        private readonly GameUserRepository $gameUserRepository,
        private readonly SteamGameRepository $steamGameRepository,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        $user = $this->getUser();
        $steamGamesCount = null;
        $hasSteamAccount = false;
        
        if ($user) {
            $steamAccount = $user->getSteamAccount();
            $hasSteamAccount = ($steamAccount !== null);
            

            
            if ($hasSteamAccount) {
                // Compter TOUS les jeux de l'utilisateur qui a un compte Steam
                $steamGamesCount = $this->entityManager->getRepository(LibraryGame::class)
                    ->createQueryBuilder('lg')
                    ->where('lg.user = :user')
                    ->setParameter('user', $user)
                    ->select('COUNT(lg.id)')
                    ->getQuery()
                    ->getSingleScalarResult();
            }
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'steamGamesCount' => $steamGamesCount,
            'hasSteamAccount' => $hasSteamAccount,
            'registeredUsers' => $this->gameUserRepository->countRegisteredUsers(),
            'onlineUsers' => $this->gameUserRepository->countOnlineUsers(new DateTimeImmutable('-15 minutes')),
        ]);
    }
}
