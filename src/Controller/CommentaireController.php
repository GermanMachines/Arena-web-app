<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Form\CommentaireType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CommentaireRepository;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
/**
 * @Route("/commentaire")
 */
class CommentaireController extends Controller
{
    /**
     * @Route("/", name="app_commentaire_index", methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager, Request $request, CommentaireRepository $postRepository): Response
    {
        $commentaire= $postRepository->findAll();
        $allposts= $postRepository->findAll();
        $commentaires =$this->get('knp_paginator') ->paginate(
        // Doctrine Query, not results
        $allposts,
        // Define the page parameter
        $request->query->getInt('page', 1),
        // Items per page
        6
        );
       
            $nbrprest=0.0 ;
            foreach($commentaire as $commentaire){
             
                 $nbrprest+=1;
             }
     
        return $this->render('commentaire/index.html.twig', [
            'commentaires' => $commentaires,
            'nbrprest' => $nbrprest
        ]);
    }

    /**
     * @Route("/new", name="app_commentaire_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($commentaire);
            $entityManager->flush();

            return $this->redirectToRoute('app_commentaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commentaire/new.html.twig', [
            'commentaire' => $commentaire,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idCom}", name="app_commentaire_show", methods={"GET"})
     */
    public function show(Commentaire $commentaire): Response
    {
        return $this->render('commentaire/show.html.twig', [
            'commentaire' => $commentaire,
        ]);
    }

    /**
     * @Route("/{idCom}/edit", name="app_commentaire_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_commentaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commentaire/edit.html.twig', [
            'commentaire' => $commentaire,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idCom}", name="app_commentaire_delete", methods={"POST"})
     */
    public function delete(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commentaire->getIdCom(), $request->request->get('_token'))) {
            $entityManager->remove($commentaire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_commentaire_index', [], Response::HTTP_SEE_OTHER);
    }
    /**
     * @Route("/s/search", name="search")
     */
    public function searchJeux(Request $request,NormalizerInterface $Normalizer,CommentaireRepository $repository):Response
    {
        $requestString=$request->get('searchValue');
        $Commentaire = $repository->findByNom($requestString);
        $jsonContent = $Normalizer->normalize($Commentaire, 'json',['Groups'=>'Commentaire:read']);
        $retour =json_encode($jsonContent);
        return new Response($retour);

    }
}
