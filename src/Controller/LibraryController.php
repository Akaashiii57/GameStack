<?php

namespace App\Controller;

use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Form\LibraryGameType;
use App\Entity\LibraryGame;
use App\Entity\Game;
use App\Form\GameType;
use App\Entity\GameUser;

#[IsGranted('ROLE_USER')]
final class LibraryController extends AbstractController
{
    #[Route('/jeux', name: 'app_jeux')]
    public function redirectToLibrary(): Response
    {
        return $this->redirectToRoute('app_library', [], 301);
    }
    
    #[Route('/library', name: 'app_library')]
    public function index(Request $request): Response
    {
        /** @var \App\Entity\GameUser $user */
        $user = $this->getUser();
        
        // Récupérer le paramètre de tri
        $sort = $request->query->get('sort', 'az'); // Par défaut: A-Z
        
        // Trier les jeux
        $libraries = $user->getLibraryGames()->toArray();
        
        if ($sort === 'za') {
            // Tri Z-A
            usort($libraries, function($a, $b) {
                return strcasecmp($b->getGame()->getTitle(), $a->getGame()->getTitle());
            });
        } else {
            // Tri A-Z (défaut)
            usort($libraries, function($a, $b) {
                return strcasecmp($a->getGame()->getTitle(), $b->getGame()->getTitle());
            });
        }

        return $this->render('library/index.html.twig', [
            'libraries' => $libraries,
            'user' => $user,
            'current_sort' => $sort,
        ]);
    }

    
    #[Route('/library/{id}', name: 'app_library_jeu')]
    public function jeu(LibraryGame $libraryGame): Response
    {
        return $this->render('library/jeu.html.twig', [
            'lg' => $libraryGame,
        ]);
    }

    #[Route('/library-search', name: 'app_library_search')]
    public function search(Request $request, GameRepository $repo): Response
    {
        $search = $request->query->get('search');
        if(!$search){
            $games = [];
        }
        else{
            $games = $repo->searchByTitle($search);
        }

        return $this->render('library/search.html.twig', [
            'games' => $games,
            'search' => $search,
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
            /** @var UploadedFile $coverFile */
            $coverFile = $form->get('cover')->getData();
            if ($coverFile) {
                $newFilename = uniqid() . '.' . $coverFile->guessExtension();
                $coverFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads/covers',
                    $newFilename
                );
                $game->setCover($newFilename);
            }
            $em->persist($libraryGame);

            $em->flush();
            return $this->redirectToRoute('app_library');
        }

        return $this->render('library/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/library/add/{id}', name: 'app_library_add', methods: ['POST'])]
    public function add(Game $game, EntityManagerInterface $em): Response
    {
        $alreadyInLibrary = $em->getRepository(LibraryGame::class)->findOneBy([
            'user' => $this->getUser(),
            'game' => $game,
        ]);

        if($alreadyInLibrary){
            $this->addFlash('error', 'Le jeu existe déjà dans votre bibliothèque');
            return $this->redirectToRoute('app_library_search');
        }
        else{
            $libraryGame = new LibraryGame();
            $libraryGame->setUser($this->getUser());
            $libraryGame->setGame($game);

            $em->persist($libraryGame);
            $em->flush();
            return $this->redirectToRoute('app_library');
        }
        
    }

    #[Route('/library/{id}/delete', name: 'app_library_delete', methods: ['POST'])]
    public function delete(LibraryGame $libraryGame, Request $request, EntityManagerInterface $em): Response
    {
        // Empêche de supprimer la carte d'un autre utilisateur
        if ($libraryGame->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        // Vérifie le token CSRF (protection contre les suppressions forcées)
        if ($this->isCsrfTokenValid('delete' . $libraryGame->getId(), $request->request->get('_token'))) {
            // On retire uniquement le lien user <-> jeu, pas le jeu du catalogue
            $em->remove($libraryGame);
            $em->flush();
            $this->addFlash('success', 'Jeu retiré de ta bibliothèque.');
        }

        return $this->redirectToRoute('app_library');
    }

    #[Route('/library/{id}/edit', name: 'app_library_edit')]
    public function edit(LibraryGame $libraryGame, Request $request, EntityManagerInterface $em): Response
    {
        if ($libraryGame->getUser() !== $this->getUser()) {
        throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(LibraryGameType::class, $libraryGame);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('app_library_jeu', [
                'id' => $libraryGame->getId(),
            ]);
        }

        return $this->render('library/edit.html.twig', [
            'form' => $form,
            'lg' => $libraryGame,
        ]);
    }

}
