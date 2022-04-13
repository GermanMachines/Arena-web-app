<?php

namespace App\Controller;

use App\Entity\MatchEq;
use App\Form\MatchEqType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Matchs;
use App\Entity\Equipe;


/**
 * @Route("/match/eq")
 */
class MatchEqController extends AbstractController
{
    /**
     * @Route("/", name="app_match_eq_index", methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $matchEqs = $entityManager
            ->getRepository(MatchEq::class)
            ->findAll();

        return $this->render('match_eq/index.html.twig', [
            'match_eqs' => $matchEqs,
        ]);
    }

    /**
     * @Route("/admin/new", name="app_match_eq_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $matchEq = new MatchEq();
        $form = $this->createForm(MatchEqType::class, $matchEq);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($matchEq);
            $entityManager->flush();

            return $this->redirectToRoute('app_match_eq_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('match_eq/new.html.twig', [
            'match_eq' => $matchEq,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idmatch}", name="app_match_eq_show", methods={"GET"})
     */
    public function show(MatchEq $matchEq): Response
    {
        return $this->render('match_eq/show.html.twig', [
            'match_eq' => $matchEq,
        ]);
    }

    /**
     * @Route("/{idmatch}/edit", name="app_match_eq_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, MatchEq $matchEq, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MatchEqType::class, $matchEq);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_match_eq_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('match_eq/edit.html.twig', [
            'match_eq' => $matchEq,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idmatch}", name="app_match_eq_delete", methods={"POST"})
     */
    public function delete(Request $request, MatchEq $matchEq, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$matchEq->getIdmatch(), $request->request->get('_token'))) {
            $entityManager->remove($matchEq);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_match_eq_index', [], Response::HTTP_SEE_OTHER);
    }
}
