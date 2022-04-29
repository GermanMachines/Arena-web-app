<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use MercurySeries\FlashyBundle\FlashyNotifier;
/**
 * @Route("/post")
 */
class PostController extends Controller
{
    /**
     * @Route("/", name="app_post_index", methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager, Request $request, PostRepository $postRepository): Response
    {
        
        $post= $postRepository->findAll();
        $allposts= $postRepository->findAll();
            $posts =$this->get('knp_paginator') ->paginate(
            // Doctrine Query, not results
            $allposts,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            6
            );
        $nbrprest=0.0 ;
       foreach($post as $post){
        
            $nbrprest+=1;
        }

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
            'nbrprest' => $nbrprest

        ]);
    }
    /**
     * @Route("/front", name="app_post_indexfrontp", methods={"GET"})
     */
    public function indexfront(EntityManagerInterface $entityManager): Response
    {    
        $posts = $entityManager
            ->getRepository(Post::class)
            ->findAll();

        return $this->render('post/indexfrontp.html.twig', [
            'posts' => $posts,
        ]);
    }
    /**
     * @Route("/new", name="app_post_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, FlashyNotifier $flashy): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $request->files->get('post')['imgPost'];
            // $file=$jeux->getImagejeux();
            $uploads_directory = $this->getParameter('uploads_directory');
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $uploads_directory,
                $filename
            );
            $post->setImgpost($filename);
            $entityManager->persist($post);
            $entityManager->flush();
             
            //$this->addFlash('success', 'Post Created! Knowledge is power!');
            $flashy->success('post added');
            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
           
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idPost}", name="app_post_show", methods={"GET"})
     */
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    /**
     * @Route("/{idPost}/edit", name="app_post_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $request->files->get('post')['imgPost'];
            // $file=$jeux->getImagejeux();
            $uploads_directory = $this->getParameter('uploads_directory');
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $uploads_directory,
                $filename
            );
            $post->setImgpost($filename);

            $entityManager->flush();

            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idPost}", name="app_post_delete", methods={"POST"})
     */
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getIdPost(), $request->request->get('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
    }
     /**
     * @Route("/s/search", name="search")
     */
    public function searchJeux(Request $request,NormalizerInterface $Normalizer,PostRepository $repository):Response
    {
        $requestString=$request->get('searchValue');
        $Post = $repository->findByNom($requestString);
        $jsonContent = $Normalizer->normalize($Post, 'json',['Groups'=>'Post:read']);
        $retour =json_encode($jsonContent);
        return new Response($retour);

    }
}
