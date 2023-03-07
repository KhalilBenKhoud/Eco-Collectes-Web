<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/article')]
class ArticleController extends AbstractController
{
    #[Route('/admin', name: 'app_article_index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('admin/formliste.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    #[Route("/Allarticles", name: "list")]
    public function getArticles(ArticleRepository $repo, SerializerInterface $serializer)
    {
        $articles = $repo->findAll();
        $json = $serializer->serialize($articles, 'json', ['groups' => "articles"]);
        return new Response($json);
    }


    #[Route('/new', name: 'app_article_new', methods: ['GET', 'POST'])]
    public function new (Request $request, ArticleRepository $articleRepository): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                $newFilename = uniqid() . '.' . $photoFile->guessExtension();

                // Move the file to a directory where your application can access it
                $photoFile->move(
                    $this->getParameter('photo_directory'),
                    $newFilename
                );

                // Update the entity with the photo filename
                $article->setPhoto($newFilename);


            }
            $articleRepository->save($article, true);
            return $this->redirectToRoute('forum_', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('article/new.html.twig', [
            'article' => $article,
            'form' => $form,

        ]);
    }

    #[Route('/{id}', name: 'app_article_show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/{id}', name: 'app_article_show2', methods: ['GET'])]


    #[Route('/{id}/edit', name: 'app_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, ArticleRepository $articleRepository): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                $newFilename = uniqid() . '.' . $photoFile->guessExtension();

                // Move the file to a directory where your application can access it
                $photoFile->move(
                    $this->getParameter('photo_directory'),
                    $newFilename
                );

                // Update the entity with the photo filename
                $article->setPhoto($newFilename);


            }
            $articleRepository->save($article, true);

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, ArticleRepository $articleRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->request->get('_token'))) {
            $articleRepository->remove($article, true);
        }

        return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route("/Article/{id}", name: "article")]
    public function ArticleId($id, NormalizerInterface $normalizer, ArticleRepository $repo)
    {
        $article = $repo->find($id);
        $articleNormalises = $normalizer->normalize($article, 'json', ['groups' => "articles"]);
        return new Response(json_encode($articleNormalises));
    }


    #[Route("newArticleJSON/new", name: "addArticleJSON")]
    public function addArticleJSON(Request $req, NormalizerInterface $Normalizer, articleRepository $articleRepository)
    {

        $article = new Article();
        $article->setTitre($req->get('titre'));
        $article->setContenu($req->get('contenu'));
        $article->setDateDeCreation($req->get('date_de_creation'));
        $article->setPhoto($req->get('photo'));
        $articleRepository->save($article, true);


        $jsonContent = $Normalizer->normalize($article, 'json', ['groups' => 'articles']);
        return new Response(json_encode($jsonContent));
    }

    #[Route("updateArticleJSON/{id}", name: "updateArticleJSON")]
    public function updateArticleJSON(Request $req, $id, NormalizerInterface $Normalizer)
    {

        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository(Article::class)->find($id);
        $article->setTitre($req->get('titre'));
        $article->setContenu($req->get('contenu'));
        $article->setDateDeCreation($req->get('date_de_creation'));
        $article->setPhoto($req->get('photo'));

        $em->flush();

        $jsonContent = $Normalizer->normalize($article, 'json', ['groups' => 'articles']);
        return new Response("Article updated successfully " . json_encode($jsonContent));
    }

    #[Route("deleteArticleJSON/{id}", name: "deleteArticleJSON")]
    public function deleteArticleJSON(Request $req, $id, NormalizerInterface $Normalizer)
    {

        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository(Article::class)->find($id);
        $em->remove($article);
        $em->flush();
        $jsonContent = $Normalizer->normalize($article, 'json', ['groups' => 'articles']);
        return new Response("Article deleted successfully " . json_encode($jsonContent));
    }







}