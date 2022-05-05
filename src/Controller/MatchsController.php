<?php

namespace App\Controller;
use App\Entity\Equipe;
use App\Entity\Matchs;
use App\Form\MatchsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Serializer\Serializer;




/**
 * @Route("/matchs")
 */
class MatchsController extends AbstractController
{
    /**
     * @Route("/", name="app_matchs_index", methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $matchs = $entityManager
            ->getRepository(Matchs::class)
            ->findAll();

        return $this->render('matchs/index.html.twig', [
            'matchs' => $matchs,
        ]);
    }

    /**
     * @Route("/front", name="app_matchs_indexfront", methods={"GET"})
     */
    public function indexq(EntityManagerInterface $entityManager): Response
    {
        {
            $match_eqs = $entityManager
                ->getRepository(Equipe::class)
                ->findBy(array('idequipe' => '94'));
    
                return $this->render('matchs/indexfront.html.twig', [
                    'equipes' => $match_eqs,
                ]);
    }

}


    /**
     * @Route("/admin/new", name="app_matchs_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $match = new Matchs();
        $form = $this->createForm(MatchsType::class, $match);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($match);
            $entityManager->flush();

            return $this->redirectToRoute('app_matchs_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('matchs/new.html.twig', [
            'match' => $match,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idmatch}", name="app_matchs_show", methods={"GET"})
     */
    public function show(Matchs $match): Response
    {
        return $this->render('matchs/show.html.twig', [
            'match' => $match,
        ]);
    }

    /**
     * @Route("/{idmatch}/edit", name="app_matchs_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Matchs $match, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MatchsType::class, $match);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_matchs_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('matchs/edit.html.twig', [
            'match' => $match,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idmatch}", name="app_matchs_delete", methods={"POST"})
     */
    public function delete(Request $request, Matchs $match, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$match->getIdmatch(), $request->request->get('_token'))) {
            $entityManager->remove($match);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_matchs_index', [], Response::HTTP_SEE_OTHER);
    }








     /**
     * @Route("/s/AllMatchs", name="AllMatchs")
     */
    public function AllMatchs(NormalizerInterface $Normalizer )
    {
    //Nous utilisons la Repository pour récupérer les objets que nous avons dans la base de données
    $repository =$this->getDoctrine()->getRepository(Matchs::class);
    $Matchs=$repository->findAll();
    //Nous utilisons la fonction normalize qui transforme en format JSON nos donnée qui sont
    //en tableau d'objet Students
    $jsonContent=$Normalizer->normalize($Matchs,'json',['groups'=>'post:read']);
    return new Response(json_encode($jsonContent));
    dump($jsonContent);
    die;
}








}
