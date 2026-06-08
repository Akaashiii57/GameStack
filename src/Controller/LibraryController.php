<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class LibraryController extends AbstractController
{
    #[Route('/jeux', name: 'app_jeux')]
    public function redirectToLibrary(): Response
    {
        return $this->redirectToRoute('app_library', [], 301);
    }

    #[Route('/library', name: 'app_library')]
    public function index(): Response
    {
        return $this->render('library/index.html.twig');
    }
}
