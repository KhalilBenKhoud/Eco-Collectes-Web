<?php

namespace App\Controller;

use App\Entity\Entreprise;
use App\Form\EntrepriseType;
use App\Repository\EntrepriseRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/entreprise')]
class EntrepriseController extends AbstractController
{
    #[Route('/', name: 'app_entreprise_index', methods: ['GET'])]
    public function index(EntrepriseRepository $entrepriseRepository): Response
    {
        return $this->render('entreprise/index.html.twig', [
            'entreprises' => $entrepriseRepository->findAll(),
        ]);
    }

    #[Route('/profile_entreprise', name: 'app_profile_entreprise_index', methods: ['GET','POST'])]
    public function profile( Request $request,EntrepriseRepository $entrepriseRepository,UserRepository $userRepository): Response
    {
        $entreprise=$entrepriseRepository->findOneBy(["ceo"=>$userRepository->find(11)]);
        if ($request->isMethod('post')) {
            $entreprise->setNom($request->get('nom'));
            $entreprise->setAdresse($request->get('adresse'));
            $entreprise->setDateCreation(new \DateTimeImmutable($request->get('date_creation')));
            $entreprise->setNumtel($request->get('numtel'));
            $entreprise->setAdresseEmail($request->get('adresse_email'));
            $entreprise->setTypeEnterprise($request->get('type_enterprise'));
            $entrepriseRepository->save($entreprise, true);

            return $this->redirectToRoute('app_profile_entreprise_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('entreprise/profile.html.twig', [
            'entreprise' => $entreprise,
        ]);
    }

    #[Route('/new/{id}', name: 'app_entreprise_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntrepriseRepository $entrepriseRepository,UserRepository $userRepository): Response
    {
        $entreprise = new Entreprise();
        $user = $userRepository->find($request->get('id'));
        if ($request->isMethod('post')) {
            $entreprise->setNom($request->get('nom'));
            $entreprise->setAdresse($request->get('adresse'));
            $entreprise->setDateCreation(new \DateTimeImmutable($request->get('date_creation')));
            $entreprise->setNumtel($request->get('numtel'));
            $entreprise->setAdresseEmail($request->get('adresse_email'));
            $entreprise->setTypeEnterprise($request->get('type_enterprise'));
            $entreprise->setCeo($user);
            $entrepriseRepository->save($entreprise, true);

            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('entreprise/new.html.twig', [
            'entreprise' => $entreprise,
            'user' => $user,
        ]);
    }

    #[Route('/{id}', name: 'app_entreprise_show', methods: ['GET'])]
    public function show(Entreprise $entreprise): Response
    {
        return $this->render('entreprise/show.html.twig', [
            'entreprise' => $entreprise,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_entreprise_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Entreprise $entreprise, EntrepriseRepository $entrepriseRepository): Response
    {
        $form = $this->createForm(EntrepriseType::class, $entreprise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entrepriseRepository->save($entreprise, true);

            return $this->redirectToRoute('app_entreprise_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('entreprise/edit.html.twig', [
            'entreprise' => $entreprise,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_entreprise_delete', methods: ['POST'])]
    public function delete(Request $request, Entreprise $entreprise, EntrepriseRepository $entrepriseRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$entreprise->getId(), $request->request->get('_token'))) {
            $entrepriseRepository->remove($entreprise, true);
        }

        return $this->redirectToRoute('app_entreprise_index', [], Response::HTTP_SEE_OTHER);
    }
}
