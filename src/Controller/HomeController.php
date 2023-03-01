<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Entity\Commentaire;

use App\Repository\CategorieRepository;
use App\Repository\CommentaireRepository;
use App\Form\CommentaireType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/home.html.twig', [
            // 'controller_name' => 'HomeController',
        ]);
    }
    #[Route('/faq', name: 'faq_')]
    public function faq(): Response
    {
        return $this->render('home/faq.html.twig', [
            // 'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/forum', name: 'forum_', methods: ['GET', 'POST'])]
    public function forum(ArticleRepository $articleRepository, CategorieRepository $categorieRepository, CommentaireRepository $commentaireRepository, Request $request): Response
    {
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentaireRepository->save($commentaire, true);

        }




        return $this->render('home/forum.html.twig', [
            'articles' => $articleRepository->findAll(),
            'categories' => $categorieRepository->findAll(),
            'commentaires' => $commentaireRepository->findAll(),

        ]);
    }
    #[Route('/forum1', name: 'forum_index', methods: ['GET', 'POST'])]
    public function forumA(ArticleRepository $articleRepository, CategorieRepository $categorieRepository, CommentaireRepository $commentaireRepository): Response
    {
        return $this->render('home/forum1.html.twig', [
            'articles' => $articleRepository->findAll(),
            'categories' => $categorieRepository->findAll(),
            'commentaires' => $commentaireRepository->findAll(),


        ]);
    }


    #[Route('/forum2', name: 'forum_index2', methods: ['GET', 'POST'])]
    public function forumB(ArticleRepository $articleRepository, CategorieRepository $categorieRepository, CommentaireRepository $commentaireRepository): Response
    {
        return $this->render('home/forum2.html.twig', [
            'articles' => $articleRepository->findAll(),
            'categories' => $categorieRepository->findAll(),
            'commentaires' => $commentaireRepository->findAll(),


        ]);
    }
    #[Route('/forum3', name: 'forum_index3', methods: ['GET', 'POST'])]
    public function forumC(ArticleRepository $articleRepository, CategorieRepository $categorieRepository, CommentaireRepository $commentaireRepository): Response
    {
        return $this->render('home/forum3.html.twig', [
            'articles' => $articleRepository->findAll(),
            'categories' => $categorieRepository->findAll(),
            'commentaires' => $commentaireRepository->findAll(),


        ]);
    }
}