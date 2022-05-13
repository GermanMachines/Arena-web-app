<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Form\CategoriesType;
use App\Repository\CategoriesRepository;
use Knp\Component\Pager\PaginatorInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;


/**
 * @Route("/categories")
 */
class CategoriesController extends AbstractController
{
    /**
     * @Route("/", name="app_categories_index", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        // Méthode findBy qui permet de récupérer les données avec des critères de filtre et de tri
        $data = $this->getDoctrine()->getRepository(Categories::class)->findBy([], ['id' => 'desc']);

        $categories = $paginator->paginate(
            $data, // Requête contenant les données à paginer (ici nos categories)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            6 // Nombre de résultats par page
        );

        return $this->render('categories/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/new", name="app_categories_new", methods={"GET", "POST"})
     */
    public function new(Request $request, CategoriesRepository $categoriesRepository): Response
    {
        $category = new Categories();
        $form = $this->createForm(CategoriesType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoriesRepository->add($category);
          #  $flashy->success('Category created!');
            return $this->redirectToRoute('app_categories_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categories/new.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_categories_show", methods={"GET"})
     */
    public function show(Categories $category): Response
    {
        return $this->render('categories/show.html.twig', [
            'category' => $category,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_categories_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Categories $category, CategoriesRepository $categoriesRepository): Response
    {
        $form = $this->createForm(CategoriesType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoriesRepository->add($category);
         #   $flashy->success('Category updated!');
            return $this->redirectToRoute('app_categories_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categories/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_categories_delete", methods={"POST"})
     */
    public function delete(Request $request, Categories $category, CategoriesRepository $categoriesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            $categoriesRepository->remove($category);
           # $flashy->success('Category deleted!');
        }

        return $this->redirectToRoute('app_categories_index', [], Response::HTTP_SEE_OTHER);
    }



    
    /**
     * @Route("/category/getcategorymobile", name="getcategorymobile")
     */
    public function getcategorymobile(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $commandes = $em->getRepository(Categories::class)->findAll();

        return $this->json($commandes, 200, [], ['groups' => 'post:read']);
    }


    /**
     * @Route("/category/addcategorymobile/new/{name}&description={description}", name="add_categories_mobile")
     */
    public function addCategoriesMobile(Request $request, NormalizerInterface $Normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        $category = new Categories();
        $category->setName($request->get('name'));
        $category->setDescription($request->get('description'));
        $em->persist($category);
        $em->flush();
        $jsonContent = $Normalizer->normalize($category, 'json', ['groups' => 'post:read']);
        return new Response(json_encode($jsonContent));
    }

    /**
     * @Route("/category/deletecategorymobile/{id}", name="delete_categories_mobile", methods={"POST"}, requirements={"id":"\d+"})
     */
    public function deleteCategoriesMobile(Request $request, $id, NormalizerInterface $Normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository(Categories::class)->find($id);

        $em->remove($category);
        $em->flush();
        $jsonContent = $Normalizer->normalize($category, 'json', ['groups' => 'post:read']);
        return new Response("information deleted" . json_encode($jsonContent));;
    }

    /**
     * @Route("/category/updatecategorymobile/{id}&name={name}&description={description}", name="update_categories_mobile")
     */
    public function updateCategoriesMobile(Request $request, $id, NormalizerInterface $Normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository(Categories::class)->find($id);
        $category->setName($request->get('name'));
        $category->setDescription($request->get('description'));

        $em->flush();
        $jsonContent = $Normalizer->normalize($category, 'json', ['groups' => 'post:read']);
        return new Response("information updated" . json_encode($jsonContent));;
    }
}
