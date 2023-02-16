<?php

namespace App\Controller;

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
}
