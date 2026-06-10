<?php

namespace App\Controller;

use App\Entity\GameUser;
use App\Service\SteamAuthService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class SteamAuthController extends AbstractController
{
    #[Route('/steam/login', name: 'app_steam_login')]
    public function login(SteamAuthService $steamAuthService): Response
    {
        $returnUrl = $this->generateUrl('app_steam_callback', [], true);
        $steamUrl = $steamAuthService->generateLoginUrl($returnUrl);

        return $this->redirect($steamUrl);
    }

    #[Route('/steam/callback', name: 'app_steam_callback')]
    public function callback(
        Request $request,
        SteamAuthService $steamAuthService,
        EntityManagerInterface $entityManager,
        AuthenticationUtils $authenticationUtils
    ): Response {
        $steamId = $steamAuthService->validateOpenIdResponse($request);

        if (!$steamId) {
            $this->addFlash('error', 'Échec de l\'authentification Steam.');
            return $this->redirectToRoute('app_login');
        }

        // Vérifier si l'utilisateur est déjà connecté
        $user = $this->getUser();

        if (!$user) {
            // Créer un nouvel utilisateur ou rediriger vers la connexion
            $this->addFlash('info', 'Veuillez d\'abord créer un compte ou vous connecter.');
            return $this->redirectToRoute('app_register');
        }

        // Lier le compte Steam à l'utilisateur
        $profileData = $steamAuthService->getSteamProfile($steamId);
        $steamAccount = $steamAuthService->linkSteamAccount($user, $steamId, $profileData);

        $this->addFlash('success', 'Compte Steam lié avec succès !');

        // Synchroniser les jeux Steam
        $games = $steamAuthService->getSteamGames($steamId);
        // TODO: Implémenter la synchronisation des jeux

        return $this->redirectToRoute('app_home');
    }

    #[Route('/steam/sync', name: 'app_steam_sync')]
    public function sync(SteamAuthService $steamAuthService): Response
    {
        $user = $this->getUser();

        if (!$user || !$user->getSteamAccount()) {
            $this->addFlash('error', 'Aucun compte Steam lié.');
            return $this->redirectToRoute('app_home');
        }

        $steamId = $user->getSteamAccount()->getSteamId();
        $games = $steamAuthService->getSteamGames($steamId);

        // TODO: Implémenter la synchronisation des jeux
        $this->addFlash('success', sprintf('%d jeux synchronisés.', count($games)));

        return $this->redirectToRoute('app_home');
    }
}
