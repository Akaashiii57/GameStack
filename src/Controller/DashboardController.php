<?php

namespace App\Controller;

use App\Repository\GameUserRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/dashboard', name: 'app_dashboard')]
#[IsGranted('ROLE_ADMIN')]
final class DashboardController extends AbstractController
{
    public function __construct(private readonly GameUserRepository $gameUserRepository)
    {
    }

    public function __invoke(): Response
    {
        $since = new DateTimeImmutable('-15 minutes');

        return $this->render('dashboard/index.html.twig', [
            'registeredUsers' => $this->gameUserRepository->countRegisteredUsers(),
            'onlineUsers' => $this->gameUserRepository->countOnlineUsers($since),
            'activeWindowMinutes' => 15,
        ]);
    }
}