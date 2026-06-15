<?php

namespace App\Controller;

use App\Entity\GameUser;
use App\Entity\SteamAccount;
use App\Security\SteamAuthenticator;
use App\Service\SteamAuthService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

final class SteamAuthController extends AbstractController
{
    #[Route('/steam/login', name: 'app_steam_login')]
    public function login(SteamAuthService $steamAuthService): Response
    {
        // Redirection vers Steam OpenID (prod et dev)
        $returnUrl = $this->generateUrl(
            'app_steam_callback',
            [],
            \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL
        );
        $steamUrl = $steamAuthService->generateLoginUrl($returnUrl);

        return $this->redirect($steamUrl);
    }

    #[Route('/steam/callback', name: 'app_steam_callback')]
    public function callback(
        Request $request,
        SteamAuthService $steamAuthService,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        SteamAuthenticator $steamAuthenticator,
        LoggerInterface $logger
    ): Response {
        // Valider la réponse OpenID de Steam
        $steamId = $steamAuthService->validateOpenIdResponse($request);
        $logger->info('Steam callback: validation OpenID', ['steamId' => $steamId]);

        if (!$steamId) {
            $logger->warning('Steam callback: validation OpenID échouée');
            $this->addFlash('error', 'Échec de l\'authentification Steam.');
            return $this->redirectToRoute('app_login');
        }

        // Récupérer le profil Steam
        $profileData = $steamAuthService->getSteamProfile($steamId) ?? [];

        $currentUser = $this->getUser();

        // --- Cas 1 : utilisateur déjà connecté → lier Steam à son compte ---
        if ($currentUser) {
            $existingSteamAccount = $entityManager->getRepository(SteamAccount::class)
                ->findOneBy(['steamId' => $steamId]);

            if ($existingSteamAccount && $existingSteamAccount->getUser()->getId() !== $currentUser->getId()) {
                // SteamID déjà lié à un autre compte : transfert
                $existingSteamAccount->setUser($currentUser);
                $existingSteamAccount->setLinkedAt(new \DateTime());
                $entityManager->flush();
                $this->addFlash('warning', 'Ce compte Steam était lié à un autre utilisateur. Il a été transféré.');
            } elseif (!$existingSteamAccount) {
                // Nouvelle liaison
                $steamAuthService->linkSteamAccount($currentUser, $steamId, $profileData);
                $this->addFlash('success', 'Compte Steam lié avec succès !');
            }

            return $this->redirectToRoute('app_home');
        }

        // --- Cas 2 : utilisateur non connecté → trouver ou créer le compte Steam ---

        // Chercher si un compte existe déjà pour ce SteamID
        $steamAccount = $entityManager->getRepository(SteamAccount::class)
            ->findOneBy(['steamId' => $steamId]);

        $isNewUser = false;

        if ($steamAccount) {
            // Compte existant : connecter l'utilisateur
            $user = $steamAccount->getUser();
        } else {
            // Nouveau compte : créer automatiquement
            $email = "steam_{$steamId}@gamestack.local";
            $existingUser = $entityManager->getRepository(GameUser::class)->findOneBy(['email' => $email]);

            if ($existingUser) {
                $user = $existingUser;
            } else {
                $user = new GameUser();
                $user->setEmail($email);
                $user->setUsername($profileData['personaname'] ?? ('steam_' . substr($steamId, -6)));
                $user->setPassword($passwordHasher->hashPassword($user, bin2hex(random_bytes(16))));
                $user->setRoles(['ROLE_USER']);
                $entityManager->persist($user);
                $entityManager->flush();
                $isNewUser = true;
            }

            // Lier le compte Steam
            $steamAuthService->linkSteamAccount($user, $steamId, $profileData);
        }

        // Synchroniser la bibliothèque Steam (première connexion ou synchro silencieuse)
        try {
            $importedCount = $steamAuthService->syncSteamGames($user, $steamId);
            $logger->info('Steam callback: synchro jeux', ['imported' => $importedCount]);
        } catch (\Throwable $e) {
            $logger->error('Steam callback: erreur synchro jeux', ['error' => $e->getMessage()]);
            $importedCount = 0;
        }

        if ($importedCount > 0) {
            $this->addFlash('success', sprintf('Connecté via Steam ! %d jeux importés depuis votre bibliothèque.', $importedCount));
        } else {
            $this->addFlash('success', 'Connecté via Steam avec succès !');
        }

        // Connecter l'utilisateur via Symfony Security
        $logger->info('Steam callback: tentative login', ['userId' => $user->getId(), 'email' => $user->getUserIdentifier()]);
        $response = $userAuthenticator->authenticateUser($user, $steamAuthenticator, $request);
        $logger->info('Steam callback: login terminé', ['hasResponse' => $response !== null]);

        return $response ?? $this->redirectToRoute('app_home');
    }

    #[Route('/steam/sync', name: 'app_steam_sync')]
    public function sync(SteamAuthService $steamAuthService, LoggerInterface $logger): Response
    {
        $user = $this->getUser();

        if (!$user) {
            $logger->warning('Steam resync: utilisateur non connecté');
            return $this->redirectToRoute('app_login');
        }

        $steamAccount = $user->getSteamAccount();
        if (!$steamAccount) {
            $logger->warning('Steam resync: aucun compte Steam lié', ['userId' => $user->getId()]);
            $this->addFlash('error', 'Aucun compte Steam lié.');
            return $this->redirectToRoute('app_home');
        }

        $steamId = $steamAccount->getSteamId();
        $logger->info('Steam resync: démarrage', [
            'userId' => $user->getId(),
            'steamId' => $steamId,
            'lastSyncAt' => $steamAccount->getLastSyncAt()?->format('Y-m-d H:i:s'),
        ]);

        try {
            $importedCount = $steamAuthService->syncSteamGames($user, $steamId);
        } catch (\Throwable $e) {
            $logger->error('Steam resync: exception pendant la synchro', [
                'userId' => $user->getId(),
                'steamId' => $steamId,
                'error' => $e->getMessage(),
            ]);
            $this->addFlash('error', 'Erreur pendant la synchronisation Steam : ' . $e->getMessage());
            return $this->redirectToRoute('app_home');
        }

        $logger->info('Steam resync: terminé', [
            'userId' => $user->getId(),
            'steamId' => $steamId,
            'importedCount' => $importedCount,
        ]);

        $this->addFlash('success', sprintf('%d jeux importés depuis Steam.', $importedCount));

        return $this->redirectToRoute('app_home');
    }
}
