<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categorie')]
class CategorieController extends AbstractController
{
    #[Route('/', name: 'app_categorie_index', methods: ['GET'])]
    public function index(CategorieRepository $categorieRepository): Response
    {
        return $this->render('categorie/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_categorie_new', methods: ['GET', 'POST'])]
    public function new (Request $request, CategorieRepository $categorieRepository): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorieRepository->save($categorie, true);

            return $this->redirectToRoute('app_categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('categorie/new.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_categorie_show', methods: ['GET'])]
    public function show(Categorie $categorie): Response
    {
        return $this->render('categorie/show.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_categorie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Categorie $categorie, CategorieRepository $categorieRepository): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorieRepository->save($categorie, true);

            return $this->redirectToRoute('app_categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('categorie/edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_categorie_delete', methods: ['POST'])]
    public function delete(Request $request, Categorie $categorie, CategorieRepository $categorieRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $categorie->getId(), $request->request->get('_token'))) {
            $categorieRepository->remove($categorie, true);
        }

        return $this->redirectToRoute('app_categorie_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route("/Allcategorie", name: "list2")]
    public function getCategory(CategorieRepository $repo, SerializerInterface $serializer)
    {
        $categorie = $repo->findAll();
        $json = $serializer->serialize($categorie, 'json', ['groups' => "categories"]);
        return new Response($json);
    }

    #[Route("/Categorie/{id}", name: "Categorie")]
    public function CategorieId($id, NormalizerInterface $normalizer, CategorieRepository $repo)
    {
        $categorie = $repo->find($id);
        $categorieNormalises = $normalizer->normalize($categorie, 'json', ['groups' => "categories"]);
        return new Response(json_encode($categorieNormalises));
    }


    #[Route("/newCategorieJSON", name: "addCategorieJSON")]
    public function addCategorieJSON(Request $req, NormalizerInterface $Normalizer, CategorieRepository $categorieRepository)
    {

        $categorie = new Categorie();
        $categorie->setRefCategorie($req->get('ref-categorie'));
        $categorieRepository->save($categorie, true);


        $jsonContent = $Normalizer->normalize($categorie, 'json', ['groups' => 'categories']);
        return new Response(json_encode($jsonContent));
    }

    #[Route("/updateCategorieJSON/{id}", name: "updateCategorieJSON")]
    public function updateCategorieJSON(Request $req, $id, NormalizerInterface $Normalizer)
    {

        $em = $this->getDoctrine()->getManager();
        $categorie = $em->getRepository(Categorie::class)->find($id);
        $categorie->setRefCategorie($req->get('ref-categorie'));
        $em->flush();

        $jsonContent = $Normalizer->normalize($categorie, 'json', ['groups' => 'categorie']);
        return new Response("Categorie updated successfully " . json_encode($jsonContent));
    }

    #[Route("/deleteCategorieJSON/{id}", name: "deleteCategorieJSON")]
    public function deleteCategorieJSON(Request $req, $id, NormalizerInterface $Normalizer)
    {

        $em = $this->getDoctrine()->getManager();
        $categorie = $em->getRepository(Categorie::class)->find($id);
        $em->remove($categorie);
        $em->flush();
        $jsonContent = $Normalizer->normalize($categorie, 'json', ['groups' => 'categories']);
        return new Response("Categorie deleted successfully " . json_encode($jsonContent));
    }

}