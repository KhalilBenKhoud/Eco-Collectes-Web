<?php

namespace App\Controller;

use App\Entity\Contrat;
use App\Entity\Invitation;
use App\Form\InvitationType;
use App\Repository\ContratRepository;
use App\Repository\EntrepriseRepository;
use App\Repository\InvitationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/invitation')]
class InvitationController extends AbstractController
{
    #[Route('/', name: 'app_invitation_index', methods: ['GET'])]
    public function index(Security $security,EntrepriseRepository $entrepriseRepository): Response
    {
        $entreprise=$entrepriseRepository->findOneBy(["ceo"=>$security->getUser()->getId()]);
        $invitations= $this->getDoctrine()->getRepository(Invitation::class)
            ->createQueryBuilder('i')
            ->innerJoin('i.contrat', 'c')
            ->where('c.enterprise = :entreprise')
            ->setParameter('entreprise', $entreprise)
            ->getQuery()
            ->getResult();
        return $this->render('invitation/index.html.twig', [
            'invitations' => $invitations,
        ]);
    }

    #[Route('/app_invitation_search', name: 'app_invitation_search', methods: ['GET','POST'])]
    public function searchinvitation(Request $request,Security $security,ContratRepository $contratRepository,EntrepriseRepository $entrepriseRepository): Response
    {
        $entreprise=$entrepriseRepository->findOneBy(["ceo"=>$security->getUser()->getId()]);
        $invitations = $this->getDoctrine()->getManager()->getRepository(Invitation::class)->findInvitationByNameDQL($request->get('nom'),$entreprise);
        return $this->render('invitation/search.html.twig', array(
            'invitations' => $invitations,
        ));
    }

    #[Route('/app_invitation_filter', name: 'app_invitation_filter', methods: ['GET','POST'])]
    public function filterinvitation(Request $request,Security $security,ContratRepository $contratRepository,EntrepriseRepository $entrepriseRepository): Response
    {
        $sea = $request->get('dat');
        $entreprise=$entrepriseRepository->findOneBy(["ceo"=>$security->getUser()->getId()]);
        $invitations= $this->getDoctrine()->getRepository(Invitation::class)
            ->createQueryBuilder('i')
            ->innerJoin('i.contrat', 'c')
            ->where('c.enterprise = :entreprise')
            ->andWhere('i.statut_invitation= :statut')
            ->setParameter('entreprise', $entreprise)
            ->setParameter('statut', $sea)
            ->getQuery()
            ->getResult();
        if($sea=="Tous"){
            $invitations= $this->getDoctrine()->getRepository(Invitation::class)
                ->createQueryBuilder('i')
                ->innerJoin('i.contrat', 'c')
                ->where('c.enterprise = :entreprise')
                ->setParameter('entreprise', $entreprise)
                ->getQuery()
                ->getResult();
        }
        return $this->render('invitation/filtrage.html.twig', array(
            'invitations' => $invitations,
        ));
    }

    #[Route('/new', name: 'app_invitation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, InvitationRepository $invitationRepository): Response
    {
        $invitation = new Invitation();
        $form = $this->createForm(InvitationType::class, $invitation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $invitationRepository->save($invitation, true);

            return $this->redirectToRoute('app_invitation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('invitation/new.html.twig', [
            'invitation' => $invitation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_invitation_show', methods: ['GET'])]
    public function show(Invitation $invitation): Response
    {
        return $this->render('invitation/show.html.twig', [
            'invitation' => $invitation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_invitation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Invitation $invitation, InvitationRepository $invitationRepository): Response
    {
        $form = $this->createForm(InvitationType::class, $invitation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $invitationRepository->save($invitation, true);

            return $this->redirectToRoute('app_invitation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('invitation/edit.html.twig', [
            'invitation' => $invitation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_invitation_delete', methods: ['POST'])]
    public function delete(Request $request, Invitation $invitation, InvitationRepository $invitationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$invitation->getId(), $request->request->get('_token'))) {
            $invitationRepository->remove($invitation, true);
        }

        return $this->redirectToRoute('app_invitation_index', [], Response::HTTP_SEE_OTHER);
    }
}
