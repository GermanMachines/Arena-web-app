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
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Repository\CommentaireRepository;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
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
    public function new(Request $request, EntityManagerInterface $entityManager): Response
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
           # $flashy->success('post added');
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
    /**
     * @Route("/pdf/download", name="post_pdf")
     */
    public function packPdf(PostRepository $repository)
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isHtml5ParserEnabled', true);
        $pdfOptions->set('isRemoteEnabled', true);

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('post/pdfListPost.html.twig', [
            'posts' => $repository->findAll(),
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => true
        ]);
    }
    /**
     * @Route("/commentaire/{idPost}", name="app_postcom")
     */
    public function PackItem(Request $request,PostRepository $postRepository, $idPost, CommentaireRepository $commentaireRepository): Response
    {   
        $post = $postRepository->find($idPost); 
        $commentaire = $commentaireRepository->getPostcom($idPost);
        
        $newCommentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $newCommentaire);

        $date = date('y-m-d');
        
        $newCommentaire->setDateCom("2022-05-07");
   
        $form->handleRequest($request);

        $newCommentaire->setIdPost($post);

        $user1 = $this->get('security.token_storage')->getToken()->getUser();

        $newCommentaire->setIdUser( $user1);

       $x=0;
     

        $array = array("fuck", "noob", "shit", "bitch");



        if ($form->isSubmitted() && $form->isValid()) {
            
            for ($i = 0; $i < sizeof($array); $i++) {               
                if(strpos($newCommentaire->getDescCom(),$array[$i]) !== false){
                    $x=1;
                   // echo $newCommentaire->getDescCom();
                    break;
                }
        }

        if($x==0){

            $commentaireRepository->add($newCommentaire);
            $newCommentaire->setIdPost($post);
           return $this->redirectToRoute('app_post_indexfrontp', [], Response::HTTP_SEE_OTHER);
       
        }else{
           # $flashy->error('Erreur mot interdit');
        }
    }

        return $this->render('post/postitem.html.twig', [
            'commentaire' => $commentaire,
            'post' => $post,
            'form' => $form->createView(),
        ]);
       
        
    }


/**
     * @Route("/s/AfficherPostMobile", name="AfficherPostMobile")
     */
    public function AfficherPostMobile(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $commandes = $em->getRepository(Post::class)->findAll();

        return $this->json($commandes, 200, [], ['groups' => 'post:read']);
    }
    /**
     * @Route("/ajouterpostMobile/new", name="ajouterpostMobile")
     */
    public function ajouterpostMobile(Request $request, NormalizerInterface $Normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        $post = new Post();
        $post->setTitre($request->get('titre'));
        $post->setAuteur($request->get('auteur'));
        $post->setImgPost($request->get('imgPost'));
        $post->setDatePost($request->get('datePost'));
        $post->setRate($request->get('rate'));
        $em->persist($post);
        $em->flush();
        $jsonContent = $Normalizer->normalize($post, 'json', ['groups' => 'post:read']);
        return new Response(json_encode($jsonContent));;
    }

    /**
     * @Route("/p/updatePostMobile/{idPost}&titre={titre}&auteur={auteur}&imgPost={imgPost}&datePost={datePost}&rate={rate}" , name="updatePostMobile")
     */
    public function updatePostMobile(Request $request, NormalizerInterface $Normalizer, $idPost)
    {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository(Post::class)->find($idPost);
        $post->setTitre($request->get('titre'));
        $post->setAuteur($request->get('auteur'));
        $post->setImgPost($request->get('imgPost'));
        $post->setDatePost($request->get('datePost'));
        $post->setRate($request->get('rate'));

        $em->flush();
        $jsonContent = $Normalizer->normalize($post, 'json', ['groups' => 'post:read']);
        return new Response("information updated" . json_encode($jsonContent));;
    }
    /**
     * @Route("/p/deletePostMobile/{idPost}", name="deletePostMobile")
     */
    public function deletePostMobile(Request $request, $idPost, NormalizerInterface $Normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository(Post::class)->find($idPost);

        $em->remove($post);
        $em->flush();
        $jsonContent = $Normalizer->normalize($post, 'json', ['groups' => 'post:read']);
        return new Response("information deleted" . json_encode($jsonContent));;
    }










}
