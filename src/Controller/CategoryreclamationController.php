<?php

namespace App\Controller;

use App\Entity\Categoryreclamation;
use App\Form\CategoryreclamationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/categoryreclamation")
 */
class CategoryreclamationController extends AbstractController
{
    /**
     * @Route("/", name="app_categoryreclamation_index", methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $categoryreclamations = $entityManager
            ->getRepository(Categoryreclamation::class)
            ->findAll();

        return $this->render('categoryreclamation/index.html.twig', [
            'categoryreclamations' => $categoryreclamations,
        ]);
    }

    /**
     * @Route("/new", name="app_categoryreclamation_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categoryreclamation = new Categoryreclamation();
        $form = $this->createForm(CategoryreclamationType::class, $categoryreclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categoryreclamation);
            $entityManager->flush();

            return $this->redirectToRoute('app_categoryreclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categoryreclamation/new.html.twig', [
            'categoryreclamation' => $categoryreclamation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_categoryreclamation_show", methods={"GET"})
     */
    public function show(Categoryreclamation $categoryreclamation): Response
    {
        return $this->render('categoryreclamation/show.html.twig', [
            'categoryreclamation' => $categoryreclamation,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_categoryreclamation_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Categoryreclamation $categoryreclamation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoryreclamationType::class, $categoryreclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_categoryreclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categoryreclamation/edit.html.twig', [
            'categoryreclamation' => $categoryreclamation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_categoryreclamation_delete", methods={"POST"})
     */
    public function delete(Request $request, Categoryreclamation $categoryreclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categoryreclamation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($categoryreclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_categoryreclamation_index', [], Response::HTTP_SEE_OTHER);
    }
}
