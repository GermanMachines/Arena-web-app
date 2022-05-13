<?php

namespace App\Controller;

use App\Entity\Categoryreclamation;
use App\Form\CategoryreclamationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @Route("/categoryreclamation")
 */
class CategoryreclamationController extends AbstractController
{
    /**
     * @Route("/categoriesReclamation/json",name="categories_json")
     */
    public function getCategoriesReclamationJson(Request $request, NormalizerInterface $normalize)
    {
        $categoriesReclamation = $this->getDoctrine()->getRepository(Categoryreclamation::class)->findAll();
        // dd($categoriesReclamation);
        $categoriesReclamationJson = $normalize->normalize($categoriesReclamation, 'json');
        return new Response(json_encode($categoriesReclamationJson));
    }
    /**
     * @Route("/addCategorieReclamationJSON",name="add_categories_rec_json")
     */
    public function addCategorieReclamation(Request $request, NormalizerInterface $normalizer)
    {


        $em = $this->getDoctrine()->getManager();

        $nomCategorie = $request->get('nom');

        $cat = new Categoryreclamation();
        $cat->setNom($nomCategorie);

        $em->persist($cat);
        $em->flush();
        $json_content = $normalizer->normalize($cat, 'json', ['groups' => 'post:read']);
        return new Response(json_encode($json_content));
    }
    /**
     * @Route("/updateCategorieReclamationJSON",name="update_cat_reclamation_json" , methods={"GET"})
     */
    public function updateReclamationByIdJSON(Request $request, NormalizerInterface $normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        $cat = new Categoryreclamation();
        $cat = $em->getRepository(Categoryreclamation::class)->find($request->get('id'));
        $cat->setNom($request->get('nom'));

        $em->flush();

        $json_content = $normalizer->normalize($cat, 'json', ['groups' => 'post:read']);
        return new Response(json_encode($json_content));
    }
    /**
     * @Route("/deleteCategorieReclamationJSON",name="delete_cat_reclamation_json" )
     */
    public function deleteReclamationByIdJSON(Request $request, NormalizerInterface $normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get("id");
        $cat = $this->getDoctrine()->getRepository(Categoryreclamation::class)->find($id);
        $em->remove($cat);
        $em->flush();
        $json_content = $normalizer->normalize($cat, 'json', ['groups' => 'post:read']);
        return new Response("Reclamation Deleted successfuly" . json_encode($json_content));
    }




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
        if ($this->isCsrfTokenValid('delete' . $categoryreclamation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($categoryreclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_categoryreclamation_index', [], Response::HTTP_SEE_OTHER);
    }
}
