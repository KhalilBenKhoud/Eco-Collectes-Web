<?php

namespace App\Controller;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use App\Security\AppCustomAuthenticator ;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\ChangePictureType;
use App\Form\EditType;
use App\Form\EditPasswordType;
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
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface ;



class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, 
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager, 
        UserAuthenticatorInterface $authenticator,
        AppCustomAuthenticator $formAuthenticator,
        SluggerInterface $slugger): Response
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
            $user->setGender( $form->get('gender')->getData() );

            $imageFile = $form->get('imageFilename')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
        
                // Move the file to the directory where images are stored
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    echo "file upload failed" ;
                }
                $user->setImageFilename($newFilename);

            }

            $entityManager->persist($user);
            $entityManager->flush();

            if($form->get('roles')->getData()[0]=="ROLE_ENTREPRISE"){
                return $this->redirectToRoute('app_entreprise_new', array('id' => $user->getId()));
            }
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

 
    #[Route('/changepicture/{id}', name: 'app_change_picture', methods: ['post'])]
    public function changePicture(Request $request , $id,
    ManagerRegistry $doctrine, SluggerInterface $slugger,
    EntityManagerInterface $entityManager, 
    ) : Response
    {

        $imageFile = $request->files->get("imageFilename");
        if ($imageFile != null) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
    
            // Move the file to the directory where images are stored
            try {
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                echo "file upload failed" ;
            }
            $user = $doctrine->getRepository(User::class)->find($id);
            $user->setImageFilename($newFilename);
            $entityManager->flush();

        }
    

            return $this->redirectToRoute('app_profile');
       

        // return $this->redirectToRoute('app_profile');

         return $this->render('registration/profile.html.twig', []);
    }


    #[Route('/profile', name: 'app_profile')]
    public function profile(Request $request ) : Response
    {
       
        return $this->render('registration/profile.html.twig', [
            
        ]);
    }

    
    
    #[Route('/edit/{id}', name: 'app_edit')]
    public function edit(Request $request ,ManagerRegistry $doctrine, $id,
    UserPasswordHasherInterface $userPasswordHasher,
    ): Response 
    {
        $user = $doctrine->getRepository(User::class)->find($id);
        $form = $this->createForm(EditType::class,$user) ;
        $form->handleRequest($request) ;

        if($form->isSubmitted() && $form->isValid())
        {
          
            $user->setRoles( $form->get('roles')->getData() );
            $token = new UsernamePasswordToken($this->getUser(), null, 'main', $this->getUser()->getRoles());
            $this->get('security.token_storage')->setToken($token);
            



        
        $em = $doctrine->getManager() ;
        $em->flush() ;
        return $this->redirectToRoute("app_profile") ; }

        return $this->render('registration/edit.html.twig', [
            "form" => $form->createView(),
         ]);
    }

    #[Route('/changepassword/{id}', name: 'app_change_password')]
    public function changepassword(Request $request ,ManagerRegistry $doctrine, $id,
    UserPasswordHasherInterface $userPasswordHasher ): Response 
    {
        $user = $doctrine->getRepository(User::class)->find($id);
        $form = $this->createForm(EditPasswordType::class,$user) ;
        $form->handleRequest($request) ;
        if($form->isSubmitted() && $form->isValid())
        {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $em = $doctrine->getManager() ;
            $em->flush() ;
            return $this->redirectToRoute("app_profile") ;
        }
        return $this->render('registration/changepassword.html.twig', [
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
