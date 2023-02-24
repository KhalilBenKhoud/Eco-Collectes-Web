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

#[Route('/contrat')]
class ContratController extends AbstractController
{
    #[Route('/', name: 'app_contrat_index', methods: ['GET'])]
    public function index(ContratRepository $contratRepository,EntrepriseRepository $entrepriseRepository,UserRepository $userRepository): Response
    {
        $entreprise=$entrepriseRepository->findOneBy(["ceo"=>$userRepository->find(11)]);
        return $this->render('contrat/index.html.twig', [
            'contrats' => $contratRepository->findBy(['enterprise'=>$entreprise->getId()]),
        ]);
    }

    #[Route('/new', name: 'app_contrat_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ContratRepository $contratRepository,EntrepriseRepository $entrepriseRepository,UserRepository $userRepository): Response
    {
        $contrat = new Contrat();
        $form = $this->createForm(ContratType::class, $contrat);
        $form->handleRequest($request);
        $entreprise=$entrepriseRepository->findOneBy(["ceo"=>$userRepository->find(11)]);
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
    public function show(Request $request,Contrat $contrat,UserRepository $userRepository,InvitationRepository $invitationRepository): Response
    {

        $users = $userRepository->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%"ROLE_COLLECTEUR"%')
            ->getQuery()
            ->getResult();
        $invitation=new Invitation();
        if ($request->isMethod('post')) {
            $invitation->setCollecteur($userRepository->find($request->get('collecteur')));
            $invitation->setDescription($request->get('description'));
            $invitation->setStatutInvitation('En attente');
            $invitation->setContrat($contrat);
            $invitation->setDateInvitation(new \DateTimeImmutable('now'));
            $invitationRepository->save($invitation, true);
            return $this->redirectToRoute('app_invitation_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('contrat/show.html.twig', [
            'contrat' => $contrat,
            'users'=>$users
        ]);
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
