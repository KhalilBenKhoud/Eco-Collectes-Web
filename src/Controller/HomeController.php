<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/home.html.twig', [
            
        ]);
    }
    #[Route('/faq', name: 'faq_')]
    public function faq(): Response
    {
        return $this->render('home/faq.html.twig', [
            
        ]);
    }

    #[Route('/ranking', name: 'app_ranking')]
    public function ranking(UserRepository $repo): Response
    {
        $users = $repo->triUsers() ;
        return $this->render('home/ranking.html.twig', [
            "users" => $users
        ]);
    }
    #[Route('/rankingDonneurs', name: 'app_ranking_donneurs')]
    public function rankingDonneurs(UserRepository $repo): Response
    {
        $users = $repo->triSpec("ROLE_DONNEUR") ;
        return $this->render('home/ranking.html.twig', [
            "users" => $users
        ]);
    }
    #[Route('/rankingCollecteurs', name: 'app_ranking_collecteurs')]
    public function rankingCollecteurs(UserRepository $repo): Response
    {
        $users = $repo->triSpec("ROLE_COLLECTEUR") ;
        return $this->render('home/ranking.html.twig', [
            "users" => $users
        ]);
    }
}
