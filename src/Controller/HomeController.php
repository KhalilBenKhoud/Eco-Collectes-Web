<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
//

}