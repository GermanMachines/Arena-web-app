<?php

namespace App\Controller;

use App\Entity\Jeux;
use App\Form\JeuxType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route("/new", name="app_jeux_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $jeux = new Jeux();
        $form = $this->createForm(JeuxType::class, $jeux);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
}
