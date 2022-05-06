<?php

namespace App\Controller;

use App\Entity\Jeux;
use App\Form\JeuxType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Knp\Component\Pager\PaginatorInterface;
use App\Repository\JeuxRepository;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use App\Entity\Avis;
use App\Form\AvisType;

/**
 * @Route("/jeux")
 */
class JeuxController extends AbstractController
{
    /**
     * @Route("/", name="app_jeux_index", methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $jeuxes = $entityManager
            ->getRepository(Jeux::class)
            ->findAll();

        return $this->render('jeux/index.html.twig', [
            'jeuxes' => $jeuxes,
        ]);
    }

        /**
     * @Route("/front", name="app_jeux_indexfront", methods={"GET"})
     */
    public function indexfront(EntityManagerInterface $entityManager , Request $request ,PaginatorInterface $paginator): Response
    {

        $avi = new Avis();
        $form = $this->createForm(AvisType::class, $avi);


        $jeuxes = $entityManager
            ->getRepository(Jeux::class)
            ->findAll();

               // Paginate the results of the query
        $jeuxes = $paginator->paginate(
            // Doctrine Query, not results
            $jeuxes,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            5
        );


        return $this->render('jeux/indexfront.html.twig', [
            'jeuxes' => $jeuxes,
        ]);
    }

     /**
     * @Route("/orderRe/{searchString}", name="orderRe")
     */
    public function index3(JeuxRepository $evenementRepository, $searchString): Response
    {
        return $this->render('jeux/index.html.twig', [
            'jeuxes' => $evenementRepository->findByExampleField($searchString),
           
        ]);
    }




    /**
     * @Route("/admin/new", name="app_jeux_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $jeux = new Jeux();
        $form = $this->createForm(JeuxType::class, $jeux);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $request->files->get('jeux')['imagejeux'];
           // $file=$jeux->getImagejeux();
            $uploads_directory = $this->getParameter('uploads_directory');
            $filename=md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $uploads_directory,
                $filename
            );
            $jeux->setImageJeux($filename);
            /*echo "<pre>";
            var_dump($file);
            die;*/

            $entityManager->persist($jeux);
            $entityManager->flush();

            return $this->redirectToRoute('app_jeux_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('jeux/new.html.twig', [
            'jeux' => $jeux,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idjeux}", name="app_jeux_show", methods={"GET"})
     */
    public function show(Jeux $jeux): Response
    {
        return $this->render('jeux/show.html.twig', [
            'jeux' => $jeux,
        ]);
    }

    /**
     * @Route("/{idjeux}/edit", name="app_jeux_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Jeux $jeux, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(JeuxType::class, $jeux);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_jeux_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('jeux/edit.html.twig', [
            'jeux' => $jeux,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idjeux}", name="app_jeux_delete", methods={"POST"})
     */
    public function delete(Request $request, Jeux $jeux, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$jeux->getIdjeux(), $request->request->get('_token'))) {
            $entityManager->remove($jeux);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_jeux_index', [], Response::HTTP_SEE_OTHER);
    }





    /**
     * @Route("/s/searchJeu", name="searchJeu")
     */
    public function searchJeux(Request $request,NormalizerInterface $Normalizer,JeuxRepository $repository):Response
    {
        $requestString=$request->get('searchValue');
        $Jeux = $repository->findByNom($requestString);
        $jsonContent = $Normalizer->normalize($Jeux, 'json',['Groups'=>'Jeux:read']);
        $retour =json_encode($jsonContent);
        return new Response($retour);

    }




}
