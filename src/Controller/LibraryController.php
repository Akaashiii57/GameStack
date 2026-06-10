<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\GameRepository;
use App\Entity\LibraryGame;
use App\Entity\Game;
use App\Form\GameType;

#[IsGranted('ROLE_USER')]
final class LibraryController extends AbstractController
{
    #[Route('/jeux', name: 'app_jeux')]
    public function redirectToLibrary(): Response
    {
        return $this->redirectToRoute('app_library', [], 301);
    }

      #[Route('/library', name: 'app_library')]
    public function index(GameRepository $repo): Response
    {

        $cardGame = $repo->findAll();

        return $this->render('library/index.html.twig', [
            'cardGame' => $cardGame,
        ]);
    }


    #[Route('/library-form', name: 'app_library_form')]
    public function form(Request $request, EntityManagerInterface $em): Response
    {

        $game = new Game();
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($game);

            $libraryGame = new LibraryGame();
            $libraryGame->setUser($this->getUser());
            $libraryGame->setGame($game);
            $em->persist($libraryGame);

            $em->flush();
            return $this->redirectToRoute('app_library');
        }

        return $this->render('library/form.html.twig', [
            'form' => $form,
        ]);
    }
}
