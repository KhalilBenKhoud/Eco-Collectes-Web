<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;


class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function read(UserRepository $repo, ManagerRegistry $doctrine): Response
    {
        $users = $doctrine
        ->getRepository(User::class)
        ->findAll() ;
        $count = $repo
        ->countofusers();

        return $this->render('admin/index.html.twig', [
            "users" => $users,
            "count" => $count
        ]);
    }
    #[Route('admin/delete/{id}', name: 'admin_delete')]
    public function delete(Request $request ,ManagerRegistry $doctrine, $id ): Response 
    {
        $user = $doctrine->getRepository(User::class)->find($id);
        $em = $doctrine->getManager() ;
        $em->remove($user) ;
        $em->flush() ;
        return $this->redirectToRoute("app_admin") ;
    }
}
