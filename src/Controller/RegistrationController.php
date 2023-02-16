<?php

namespace App\Controller;

use App\Security\AppCustomAuthenticator ;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Doctrine\Persistence\ManagerRegistry;




class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher,
     EntityManagerInterface $entityManager, 
     UserAuthenticatorInterface $authenticator
     ,AppCustomAuthenticator $formAuthenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $user->setRoles( $form->get('roles')->getData() );
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $authenticator->authenticateUser(
                $user, 
                $formAuthenticator, 
                $request); 



            return $this->redirectToRoute('app_profile');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            
        ]);
    }

    #[Route('/profile', name: 'app_profile')]
    public function profile(Request $request ) : Response
    {
        return $this->render('registration/profile.html.twig', [
            
        ]);
    }
   
    #[Route('/edit/{id}', name: 'app_edit')]
    public function edit(Request $request ,ManagerRegistry $doctrine, $id,
    UserPasswordHasherInterface $userPasswordHasher ): Response 
    {
        $user = $doctrine->getRepository(User::class)->find($id);
        $form = $this->createForm(RegistrationFormType::class,$user) ;
        $form->handleRequest($request) ;
        if($form->isSubmitted() && $form->isValid())
        {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $user->setRoles( $form->get('roles')->getData() );

            $em = $doctrine->getManager() ;
            $em->flush() ;
            return $this->redirectToRoute("app_profile") ;
        }
        return $this->render('registration/edit.html.twig', [
            "form" => $form->createView(),
         ]);
    }
    #[Route('/delete/{id}', name: 'app_delete')]
    public function delete(Request $request ,ManagerRegistry $doctrine, $id ): Response 
    {
        $user = $doctrine->getRepository(User::class)->find($id);
        $em = $doctrine->getManager() ;
        $em->remove($user) ;
        $em->flush() ;
        return $this->redirectToRoute("app_home") ;
    }

}
