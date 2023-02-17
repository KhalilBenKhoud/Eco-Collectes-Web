<?php

namespace App\Controller;

use App\Entity\Collect;
use App\Form\CollectType;
use App\Repository\CollectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('   ')]
class CollectController extends AbstractController
{
    #[Route('/', name: 'app_collect_index', methods: ['GET'])]
    public function index(CollectRepository $collectRepository): Response
    {
        return $this->render('collect/index.html.twig', [
            'collects' => $collectRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_collect_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CollectRepository $collectRepository): Response
    {
        $collect = new Collect();
        $form = $this->createForm(CollectType::class, $collect);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $collectRepository->save($collect, true);

            return $this->redirectToRoute('app_collect_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('collect/new.html.twig', [
            'collect' => $collect,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_collect_show', methods: ['GET'])]
    public function show(Collect $collect): Response
    {
        return $this->render('collect/show.html.twig', [
            'collect' => $collect,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_collect_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Collect $collect, CollectRepository $collectRepository): Response
    {
        $form = $this->createForm(CollectType::class, $collect);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $collectRepository->save($collect, true);

            return $this->redirectToRoute('app_collect_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('collect/edit.html.twig', [
            'collect' => $collect,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_collect_delete', methods: ['POST'])]
    public function delete(Request $request, Collect $collect, CollectRepository $collectRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$collect->getId(), $request->request->get('_token'))) {
            $collectRepository->remove($collect, true);
        }

        return $this->redirectToRoute('app_collect_index', [], Response::HTTP_SEE_OTHER);
    }
}
