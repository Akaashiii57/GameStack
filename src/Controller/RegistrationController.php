<?php

namespace App\Controller;

use App\Entity\GameUser;
use App\Form\RegistrationFormType;
use App\Service\SteamAuthService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher, 
        EntityManagerInterface $entityManager,
        SteamAuthService $steamAuthService
    ): Response
    {
        $user = new GameUser();
        
        // Vérifier si des données Steam sont en session
        $steamData = $request->getSession()->get('steam_auth_data');
        if ($steamData) {
            // Pré-remplir avec les données Steam
            $user->setUsername('steam_user_' . substr($steamData['steam_id'], -6));
            if ($steamData['profile_data'] && isset($steamData['profile_data']['personaname'])) {
                $user->setUsername($steamData['profile_data']['personaname']);
            }
        }
        
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            // Lier automatiquement Steam si des données sont en session
            $steamData = $request->getSession()->get('steam_auth_data');
            if ($steamData) {
                $steamAuthService->linkSteamAccount($user, $steamData['steam_id'], $steamData['profile_data'] ?? null);
                
                // Nettoyer la session
                $request->getSession()->remove('steam_auth_data');
                
                $this->addFlash('success', 'Compte Steam lié avec succès !');
            }

            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
