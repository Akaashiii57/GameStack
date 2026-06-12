<?php

namespace App\Controller;

use App\Entity\GameUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class TestController extends AbstractController
{
    #[Route('/test/verify-auth', name: 'test_verify_auth')]
    public function verifyAuth(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = $entityManager->getRepository(GameUser::class)->findOneBy(['email' => 'test@example.com']);
        
        if (!$user) {
            return new Response('Utilisateur non trouvé');
        }
        
        // Tester la vérification du mot de passe
        $isValid = $passwordHasher->isPasswordValid($user, 'password123');
        
        return new Response('Mot de passe valide: ' . ($isValid ? 'OUI' : 'NON'));
    }
    
    #[Route('/test/create-user', name: 'test_create_user')]
    public function createUser(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        // Vérifier si l'utilisateur existe déjà
        $existingUser = $entityManager->getRepository(GameUser::class)->findOneBy(['email' => 'test@example.com']);
        
        if ($existingUser) {
            return new Response('Utilisateur existe déjà');
        }
        
        // Créer un nouvel utilisateur
        $user = new GameUser();
        $user->setEmail('test@example.com');
        $user->setUsername('testuser');
        $user->setRoles(['ROLE_USER']);
        
        // Hasher le mot de passe
        $hashedPassword = $passwordHasher->hashPassword($user, 'password123');
        $user->setPassword($hashedPassword);
        
        $entityManager->persist($user);
        $entityManager->flush();
        
        return new Response('Utilisateur créé avec email: test@example.com, mot de passe: password123');
    }
}