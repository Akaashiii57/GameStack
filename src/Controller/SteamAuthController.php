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
        // Mode développement : simuler l'authentification Steam
        if ($_ENV['APP_ENV'] === 'dev') {
            // Rediriger directement vers le callback avec un SteamID de test
            return $this->redirectToRoute('app_steam_callback', ['dev_mode' => 'true', 'steamid' => '76561198012345678']);
        }

        // Mode production : utiliser l'authentification Steam réelle
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
        // Mode développement : utiliser un SteamID de test
        if ($request->query->get('dev_mode') === 'true') {
            $steamId = $request->query->get('steamid', '76561198012345678');
        } else {
            // Mode production : validation OpenID réelle
            $steamId = $steamAuthService->validateOpenIdResponse($request);
        }

        if (!$steamId) {
            $this->addFlash('error', 'Échec de l\'authentification Steam.');
            return $this->redirectToRoute('app_login');
        }

        // Vérifier si l'utilisateur est déjà connecté
        $user = $this->getUser();

        if (!$user) {
            // Stocker les données Steam en session pour pré-remplir l'inscription
            $request->getSession()->set('steam_auth_data', [
                'steam_id' => $steamId,
                'profile_data' => $profileData ?? null
            ]);
            
            $this->addFlash('info', 'Presque terminé ! Complétez votre inscription.');
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
