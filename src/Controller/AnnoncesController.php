<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Form\AnnoncesType;
use App\Form\ContactType;
use App\Repository\AnnoncesRepository;
use SebastianBergmann\Environment\Console;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/annonces')]
class AnnoncesController extends AbstractController
{
    #[Route('/index', name: 'app_annonces_index', methods: ['GET'])]
    public function index(AnnoncesRepository $annoncesRepository ): Response
    {
        return $this->render('annonces/index.html.twig', [
            'annonces' => $annoncesRepository->findAll(),
        ]);
    }

    #[Route('/view', name: 'app_annonces_front', methods: ['GET'])]
    public function front(AnnoncesRepository $annoncesRepository): Response
    {
        return $this->render('annonces/AnnoncesF.html.twig', [
            'annonces' => $annoncesRepository->findAll(),
        ]);
    }

    #[Route('/{id}/upvote', name: 'app_annonces_upvote', methods: ['POST'])]
    public function upvote(Annonces $annonce, Request $request): Response
    {
        $annonce->setRating($annonce->getRating() + 1);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($annonce);
        $entityManager->flush();
    
        return $this->render('annonces/show.html.twig', [
            'annonce' => $annonce,
        ]);
    }
    
    #[Route('/{id}/downvote', name: 'app_annonces_downvote', methods: ['POST'])]
    public function downvote(Annonces $annonce, Request $request): Response
    {
        $annonce->setRating($annonce->getRating() - 1);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($annonce);
        $entityManager->flush();
    
        return $this->render('annonces/show.html.twig', [
            'annonce' => $annonce,
        ]);
    }

    #[Route('/new', name: 'app_annonces_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AnnoncesRepository $annoncesRepository): Response
    {
        $annonce = new Annonces();
        $user = $this -> getUser();
        $annonce->setJoinUser($user);
        $form = $this->createForm(AnnoncesType::class, $annonce);
        $form->handleRequest($request);

        $annonce->setDateModification(new \DateTimeImmutable());
        $annonce->setDateCreation(new \DateTimeImmutable());

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imgUrl')->getData();
            if($imageFile){
                $imageFileName = md5(uniqid()) . '.' . $imageFile->guessExtension();
                $imageFile->move($this->getParameter('image_directory'), $imageFileName);
    
                $annonce->setImgUrl($imageFileName);
            }

            $annoncesRepository->save($annonce, true);

            return $this->redirectToRoute('app_annonces_front', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('annonces/new.html.twig', [
            'user' => $user,
            'annonce' => $annonce,
            'form' => $form,
        ]);
    }

    #[Route('/search', name: 'app_annonces_search', methods: ['GET', 'POST'])]
    public function search(Request $request, AnnoncesRepository $annoncesRepository)
{
    $query = $request->query->get('q');

    $annonces = $annoncesRepository->search($query);

    return $this->render('annonces/AnnoncesF.html.twig', [
        'annonces' => $annonces,
    ]);
}


    #[Route('/email/{id}', name: 'app_annonces_email', methods: ['GET','POST'])]
    public function email(int $id, AnnoncesRepository $annoncesRepository, Request $request, MailerInterface $mailer)
    {
        $annonce = $annoncesRepository -> find($id);
        $form = $this->createForm(ContactType::class, null, [
            'data' => ['annonce => $annonce'],
        ]);
        dump($form);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $message = sprintf("<p>Nouveau mail envoyé par %s</p><p>%s</p>", $formData['email'], $formData['message'], "<p> pour repondre, utilisez cette adresse.</p>");
            
            
            // Create the email
            $email = (new Email())
                ->from("ecocollectes@gmail.com")
                ->to($annonce->getJoinUser()->getEmail())
                ->subject('Contact from your announcement')
                ->html($message);
            // Send the email
            try {
                $mailer->send($email);
            } catch (\Exception $e) {
                $this->addFlash('error', 'An error occurred while sending the email: ' . $e->getMessage());
                return $this->redirectToRoute('app_annonces_show', ['id' => $annonce->getId()]);
            }

            return $this->render('annonces/contact_success.html.twig', [
                'annonce' => $annonce
            ]);
        }

        return $this->render('annonces/contact.html.twig', [
            'form' => $form->createView(),
            'annonce' => $annonce
        ]);
    }

    #[Route('/{id}', name: 'app_annonces_show', methods: ['GET','POST'])]
    public function show(Annonces $annonce): Response
    {
        return $this->render('annonces/show.html.twig', [
            'annonce' => $annonce,
        ]);
    }

    #[Route('/contact/{id}', name: 'app_annonces_contact', methods: ['GET'])]
    public function contact(Annonces $annonce): Response
    {
        
        return $this->render('annonces/contact.html.twig', [
            'annonce' => $annonce,
        ]);
    }

    
    
    #[Route('/{id}/edit', name: 'app_annonces_edit', methods: ['GET', 'POST'])]
    public function edit(SessionInterface $session, Request $request, Annonces $annonce, AnnoncesRepository $annoncesRepository): Response
    {
        if ($this->getUser()->email != $annonce -> joinUser -> email) {
            return $this->render('annonces/ineligible.html.twig', [
                'annonce' => $annonce,
            ]);
        } else {
            $form = $this->createForm(AnnoncesType::class, $annonce);
            $form->handleRequest($request);
        
            
            if ($form->isSubmitted() && $form->isValid()) {
                $annonce->setDateModification(new \DateTimeImmutable());
                $annoncesRepository->save($annonce, true);
        
                return $this->redirectToRoute('app_annonces_index', [], Response::HTTP_SEE_OTHER);
            }
        
            return $this->renderForm('annonces/edit.html.twig', [
                'annonce' => $annonce,
                'form' => $form,
            ]);
        }
    }
    
    #[Route('/{id}', name: 'app_annonces_delete', methods: ['POST'])]
    public function delete(Request $request, Annonces $annonce, AnnoncesRepository $annoncesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$annonce->getId(), $request->request->get('_token'))) {
            $annoncesRepository->remove($annonce, true);
        }

        return $this->redirectToRoute('app_annonces_index', [], Response::HTTP_SEE_OTHER);
    }


    
}
