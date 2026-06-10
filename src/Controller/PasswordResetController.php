<?php

namespace App\Controller;

use App\Entity\GameUser;
use App\Form\PasswordResetType;
use App\Repository\GameUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class PasswordResetController extends AbstractController
{
    #[Route('/password-reset', name: 'app_password_reset')]
    public function index(
        Request $request,
        GameUserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(PasswordResetType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            $user = $userRepository->findOneBy([
                'email' => $data['email'],
                'Username' => $data['username']
            ]);

            if ($user) {
                $hashedPassword = $passwordHasher->hashPassword($user, $data['newPassword']);
                $user->setPassword($hashedPassword);
                
                $entityManager->flush();

                $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès.');
                return $this->redirectToRoute('app_login');
            } else {
                $this->addFlash('error', 'Aucun utilisateur trouvé avec cet email et ce pseudo.');
            }
        }

        return $this->render('password_reset/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}