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
use App\Entity\User;
use App\Entity\Post;




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
     * @Route("/front", name="app_commentaire_indexfrontc", methods={"GET"})
     */
    public function indexfront(EntityManagerInterface $entityManager): Response
    {
        $commentaires = $entityManager
            ->getRepository(Commentaire::class)
            ->findAll();

        return $this->render('commentaire/indexfrontc.html.twig', [
            'commentaires' => $commentaires,
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
     /**
     * @Route("/delete/{idCom}", name="app_commentaire_deletefront", methods={"POST"})
     */
    public function deletefront(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $commentaire->getIdCom(), $request->request->get('_token'))) {
            $entityManager->remove($commentaire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_commentaire_indexfrontc', [], Response::HTTP_SEE_OTHER);
    }




   /**
     * @Route("/s/AfficherComMobile", name="AfficherComMobile")
     */
    public function AfficherComMobile(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $commandes = $em->getRepository(Commentaire::class)->findAll();

        return $this->json($commandes,200,[],['groups'=>'post:read']);

        //http://127.0.0.1:8000/AfficherCategorieMobile

    }

    /**
     * @Route("/s/getAllComJSONFront",name="getAlComJSONFront" , methods={"GET"})
     */
    public function getAlComJSONFront(Request $request, NormalizerInterface $normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        $idUser = $request->get("iduser");

        $avis = $em
            ->getRepository(Commentaire::class)
            ->findBy(array('idUser' => $idUser));

        $json_content = $normalizer->normalize($avis, 'json', ['groups' => 'post:read']);
        return new Response(json_encode($json_content));
    }


    /**
     * @Route("/s/deleteCommentaireJSON",name="delete_Commentaire_json" , methods={"GET"})
     */
    public function deleteCommentaireJSON(Request $request, NormalizerInterface $normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get("idCom");

        $avis = $this->getDoctrine()->getRepository(Commentaire::class)->find($id);
        $em->remove($avis);
        $em->flush();
        $json_content = $normalizer->normalize($avis, 'json', ['groups' => 'post:read']);
        return new Response("Commentaire Deleted successfuly" . json_encode($json_content));
    }


    /**
     * @Route("/s/addCommentJSON", name="add_Comment_jsona", methods={"GET"})
     */
    public function addCommentJSON(Request $request, NormalizerInterface $normalizer)
    {
        $em = $this->getDoctrine()->getManager();

        $idUser = $request->get('idUser');
        $user = $this->getDoctrine()->getRepository(User::class)->find($idUser);

        $idPost = $request->get('idPost');
        $Post = $this->getDoctrine()->getRepository(Post::class)->find($idPost);

        $Commentaire = new Commentaire();
        $Commentaire->setDescCom($request->get('descCom'));
        $Commentaire->setDateCom($request->get('dateCom'));
        $Commentaire->setIdUser($user); //must pass User
        $Commentaire->setIdPost($Post);


        $em->persist($Commentaire);
        $em->flush();
        $json_content = $normalizer->normalize($Commentaire, 'json', ['groups' => 'post:read']);
        return new Response(json_encode($json_content));
    }

}

