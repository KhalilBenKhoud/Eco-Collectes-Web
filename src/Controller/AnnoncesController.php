<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Form\AnnoncesType;
use App\Repository\AnnoncesRepository;
use SebastianBergmann\Environment\Console;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    #[Route('/annonces/{id}/upvote', name: 'app_annonces_upvote', methods: ['GET'])]
    public function upvote(Int $id, AnnoncesRepository $annoncesRepository): Response
    {
        $annonce = $annoncesRepository -> find($id);    
        $annonce->setRating($annonce->getRating() + 1);
        $annoncesRepository->save($annonce);

        return $this->redirectToRoute('app_annonces_show', ['id' => $annonce->getId()]);
    }
    
    #[Route('/annonces/{id}/downvote', name: 'app_annonces_downvote', methods: ['GET'])]
    public function downvote(Int $id, AnnoncesRepository $annoncesRepository): Response
    {
        $annonce = $annoncesRepository -> find($id);
        $annonce->setRating($annonce->getRating() - 1);
        $annoncesRepository->save($annonce);
        
        return $this->redirectToRoute('app_annonces_show', ['id' => $annonce->getId()]);
    }

    #[Route('/new', name: 'app_annonces_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AnnoncesRepository $annoncesRepository): Response
    {
        $user = $this -> getUser();
        $annonce = new Annonces();
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

            return $this->redirectToRoute('app_annonces_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('annonces/new.html.twig', [
            'user' => $user,
            'annonce' => $annonce,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_annonces_show', methods: ['GET'])]
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
    public function edit(Request $request, Annonces $annonce, AnnoncesRepository $annoncesRepository): Response
    {
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
    
    #[Route('/{id}', name: 'app_annonces_delete', methods: ['POST'])]
    public function delete(Request $request, Annonces $annonce, AnnoncesRepository $annoncesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$annonce->getId(), $request->request->get('_token'))) {
            $annoncesRepository->remove($annonce, true);
        }

        return $this->redirectToRoute('app_annonces_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/email', name: 'app_annonces_email', methods: ['GET', 'POST'])]
    public function email(Request $request, MailerInterface $mailer, Annonces $annonce): Response
    {
        $form = $this->createForm(EmailType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $email = (new Email())
                ->from($this->getUser()->getUserIdentifier())
                ->to($annonce->getUser()->getEmail())
                ->subject($data['subject'])
                ->text($data['body']);

            $mailer->send($email);

            $this->addFlash('success', 'Your message has been sent successfully.');

            return $this->redirectToRoute('app_annonces_show', ['id' => $annonce->getId()]);
        }

        return $this->renderForm('annonces/email.html.twig', [
            'annonce' => $annonce,
            'form' => $form,
        ]);
    }



    
}
