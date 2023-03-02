<?php

namespace App\Controller;

use App\Entity\Contrat;
use App\Entity\Invitation;
use App\Entity\User;
use App\Form\ContratType;
use App\Repository\ContratRepository;
use App\Repository\EntrepriseRepository;
use App\Repository\InvitationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

#[Route('/contrat')]
class ContratController extends AbstractController
{
    #[Route('/', name: 'app_contrat_index', methods: ['GET'])]
    public function index(ContratRepository $contratRepository,Security $security,EntrepriseRepository $entrepriseRepository): Response
    {
        $entreprise=$entrepriseRepository->findOneBy(["ceo"=>$security->getUser()->getId()]);
        return $this->render('contrat/index.html.twig', [
            'contrats' => $contratRepository->findBy(['enterprise'=>$entreprise->getId()]),
        ]);
    }

    #[Route('/app_contrat_search', name: 'app_contrat_search', methods: ['GET','POST'])]
    public function searchcontrat(Request $request,Security $security,ContratRepository $contratRepository,EntrepriseRepository $entrepriseRepository): Response
    {
        $entreprise=$entrepriseRepository->findOneBy(["ceo"=>$security->getUser()->getId()]);
        $contrats = $this->getDoctrine()->getManager()->getRepository(Contrat::class)->findContratByNameDQL($request->get('nom'),$entreprise);
        return $this->render('contrat/search.html.twig', array(
            'contrats' => $contrats,
        ));
    }

    #[Route('/app_contrat_filter', name: 'app_contrat_filter', methods: ['GET','POST'])]
    public function filtercontrat(Request $request,Security $security,ContratRepository $contratRepository,EntrepriseRepository $entrepriseRepository): Response
    {
        $sea = $request->get('dat');
        $entreprise=$entrepriseRepository->findOneBy(["ceo"=>$security->getUser()->getId()]);

        $contrats = $this->getDoctrine()->getManager()->getRepository(Contrat::class)->findBy(['statut_contrat'=>$sea,'enterprise'=>$entreprise->getId()]);
        if($sea=="Tous"){
            $contrats =$contratRepository->findBy(['enterprise'=>$entreprise->getId()]);
        }
        return $this->render('contrat/filtrage.html.twig', array(
            'contrats' => $contrats,
        ));
    }

    #[Route('/new', name: 'app_contrat_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ContratRepository $contratRepository,Security $security,EntrepriseRepository $entrepriseRepository): Response
    {
        $contrat = new Contrat();
        $form = $this->createForm(ContratType::class, $contrat);
        $form->handleRequest($request);
        $entreprise=$entrepriseRepository->findOneBy(["ceo"=>$security->getUser()->getId()]);
        if ($form->isSubmitted() && $form->isValid()) {
            $contrat->setStatutContrat("En Attente");
            $contrat->setEnterprise($entreprise);
            $contratRepository->save($contrat, true);

            return $this->redirectToRoute('app_contrat_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('contrat/new.html.twig', [
            'contrat' => $contrat,
            'form' => $form,

        ]);
    }

    #[Route('/{id}', name: 'app_contrat_show', methods: ['GET','POST'])]
    public function show(Request $request,MailerInterface $mailer, \Twig\Environment $twig,Contrat $contrat,UserRepository $userRepository,InvitationRepository $invitationRepository): Response
    {

        $users = $userRepository->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%"ROLE_COLLECTEUR"%')
            ->getQuery()
            ->getResult();
        $invitation=new Invitation();
        if ($request->isMethod('post')) {
            $collecteur=$userRepository->find($request->get('collecteur'));
            $invitation->setCollecteur($collecteur);
            $invitation->setDescription($request->get('description'));
            $invitation->setStatutInvitation('En attente');
            $invitation->setContrat($contrat);
            $invitation->setDateInvitation(new \DateTimeImmutable('now'));
            $invitationRepository->save($invitation, true);
            $htmlBody = $twig->render('invitation/email_template.html.twig',
                ['invitation_id' => $invitation->getId(),
                    'id_contrat'=>$contrat->getId(),
                    'id_collecteur'=>$collecteur->getId()]);
            $email = (new TemplatedEmail())
                ->from('eco.collect.esprit@gmail.com')
                ->to($collecteur->getEmail())
                ->subject('Invitation Request')
                ->html($htmlBody);
            $mailer->send($email);

            //return $this->redirectToRoute('app_invitation_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('contrat/show.html.twig', [
            'contrat' => $contrat,
            'users'=>$users
        ]);
    }
    #[Route('/ContratInvitationRequest/{invitation_id}/{id_contrat}/{id_collecteur}/{action}', name: 'app_contrat_handleConInv', methods: ['GET', 'POST'])]
    public function handleContratInvitationRequest(Request $request,UserRepository $userRepository, InvitationRepository $invitationRepository, ContratRepository $contratRepository): Response
    {
        if($request->get('action')=='refuser'){
            $invitation=$invitationRepository->find($request->get('invitation_id'));
            $invitation->setStatutInvitation('Refuse');
            $invitationRepository->save($invitation, true);
        }
        else {
            $invitation=$invitationRepository->find($request->get('invitation_id'));
            $invitation->setStatutInvitation('Accepte');
            $invitationRepository->save($invitation, true);
            $contrat=$contratRepository->find($request->get('id_contrat'));
            $contrat->setCollecteur($userRepository->find($request->get('id_collecteur')));
            $contratRepository->save($contrat, true);
        }
        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/edit', name: 'app_contrat_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Contrat $contrat, ContratRepository $contratRepository): Response
    {
        $form = $this->createForm(ContratType::class, $contrat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contratRepository->save($contrat, true);

            return $this->redirectToRoute('app_contrat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('contrat/edit.html.twig', [
            'contrat' => $contrat,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_contrat_delete', methods: ['POST','GET'])]
    public function delete(Request $request, Contrat $contrat, ContratRepository $contratRepository): Response
    {
        $contratRepository->remove($contrat, true);
        return $this->redirectToRoute('app_contrat_index', [], Response::HTTP_SEE_OTHER);
    }
}
