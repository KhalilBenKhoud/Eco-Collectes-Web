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
use Symfony\Component\Serializer\Normalizer\NormalizerInterface ;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class UserApiController extends AbstractController
{
    #[Route('/registerApi', name: 'app_register_api')]
    public function register(Request $request, 
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager, 
        NormalizerInterface $Normalizer 
       ): Response
    {
        $user = new User();
       
            // encode the plain password
            $user->setEmail($request->get('email')) ;
            $user->setUsername($request->get('username')) ;
            $user->setPhone($request->get('phone')) ;
            $user->setAddress($request->get('address')) ;
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $request->get('password')
                )
            );
            $user->setGender( $request->get('gender') );


            $entityManager->persist($user);
            $entityManager->flush();

           
            $jsonContent = $Normalizer->normalize($user,'json',['groups' => 'users']) ;
            return new Response(json_encode($jsonContent)) ;
      
    }

    #[Route('/deleteApi/{id}', name: 'app_delete_api')]
    public function delete(Request $request ,ManagerRegistry $doctrine, $id,  NormalizerInterface $Normalizer     ): Response 
    {
        $user = $doctrine->getRepository(User::class)->find($id);
        $em = $doctrine->getManager() ;
        $em->remove($user) ;
        $em->flush() ;
        $jsonContent = $Normalizer->normalize($user,'json',['groups' => 'users']) ;
        return new Response(json_encode($jsonContent)) ;

    }
    #[Route('/showAllApi', name: 'app_showAll_api')]
    public function showAll(Request $request ,ManagerRegistry $doctrine,  NormalizerInterface $Normalizer     ): Response 
    {
        $users = $doctrine->getRepository(User::class)->findAll();
       
        $jsonContent = $Normalizer->normalize($users,'json',['groups' => 'users']) ;
        return new Response(json_encode($jsonContent)) ;

    }

    #[Route('/updateApi/{id}', name: 'app_update_api')]
    public function update(Request $request ,
    UserPasswordHasherInterface $userPasswordHasher,
    ManagerRegistry $doctrine, $id,
      NormalizerInterface $Normalizer     ): Response 
    {
        $user = $doctrine->getRepository(User::class)->find($id);
        
        $user->setEmail($request->get('email')) ;
        $user->setUsername($request->get('username')) ;
        $user->setPhone($request->get('phone')) ;
        $user->setAddress($request->get('address')) ;
        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $request->get('password')
            )
        );

        $user->setGender( $request->get('gender') );
        $em = $doctrine->getManager() ;
        $em->flush() ;
        $jsonContent = $Normalizer->normalize($user,'json',['groups' => 'users']) ;
        return new Response(json_encode($jsonContent)) ;

    }

    #[Route(path: '/authApi', name: 'app_login_api')]
    public function login(AuthenticationUtils $authenticationUtils,
    NormalizerInterface $Normalizer): Response
    {
    
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        $jsonContent = $Normalizer->normalize( $lastUsername,'json',['groups' => 'users']) ;
        return new Response(json_encode($jsonContent)) ;
    }

}
