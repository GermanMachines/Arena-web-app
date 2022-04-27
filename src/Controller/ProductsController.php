<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Products;
use App\Form\ProductsType;
use App\Repository\ProductsRepository;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Component\Pager\PaginatorInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/products")
 */
class ProductsController extends AbstractController
{
    /**
     * @Route("/", name="app_products_index", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        // Méthode findBy qui permet de récupérer les données avec des critères de filtre et de tri
        $data = $this->getDoctrine()->getRepository(Products::class)->findBy([], ['id' => 'desc']);

        $products = $paginator->paginate(
            $data, // Requête contenant les données à paginer (ici nos products)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            6 // Nombre de résultats par page
        );

        $pieChart = new PieChart();

        $productsData = $this->getDoctrine()->getRepository(Products::class)->findAll();
        $categoriesData = $this->getDoctrine()->getRepository(Categories::class)->findAll();

        // dd($categoriesData);

        $charts = array(['Products', 'Number per Category']);
        // dd($charts);
        foreach ($categoriesData as $c) {
            $catN = 0;
            foreach ($productsData as $p) {
                if ($c == $p->getIdcat()) {
                    $catN++;
                }
            }

            array_push($charts, [$c->getName(), $catN]);
        }


        // dd($charts);

        $pieChart->getData()->setArrayToDataTable(
            $charts
        );

        // dd($pieChart);

        $pieChart->getOptions()->setTitle('Products Number per Category');
        $pieChart->getOptions()->setHeight(400);
        $pieChart->getOptions()->setWidth(400);
        $pieChart->getOptions()->getTitleTextStyle()->setColor('#07600');
        $pieChart->getOptions()->getTitleTextStyle()->setFontSize(25);

        return $this->render('products/index.html.twig', [
            'products' => $products,
            'piechart' => $pieChart
        ]);
    }

    /**
     * @Route("/new", name="app_products_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, FlashyNotifier $flashy): Response
    {
        $product = new Products();
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $request->files->get('products')['image'];
            $uploads_directory = $this->getParameter('uploads_directory');
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $uploads_directory,
                $filename
            );
            $product->setImage($filename);
            $entityManager->persist($product);
            $entityManager->flush();
            $flashy->success('Product created!');
            return $this->redirectToRoute('app_products_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('products/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_products_show", methods={"GET"}, requirements={"id":"\d+"})
     */
    public function show(Products $product): Response
    {
        return $this->render('products/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_products_edit", methods={"GET", "POST"}, requirements={"id":"\d+"})
     */
    public function edit(Request $request, Products $product, EntityManagerInterface $entityManager, FlashyNotifier $flashy): Response
    {
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $request->files->get('products')['image'];
            $uploads_directory = $this->getParameter('uploads_directory');
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $uploads_directory,
                $filename
            );
            $product->setImage($filename);
            $entityManager->persist($product);
            $entityManager->flush();
            $flashy->success('Product updated!');
            return $this->redirectToRoute('app_products_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('products/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_products_delete", methods={"POST"}, requirements={"id":"\d+"})
     */
    public function delete(Request $request, Products $product, ProductsRepository $productsRepository, FlashyNotifier $flashy): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            $productsRepository->remove($product);
            $flashy->success('Product deleted!');
        }

        return $this->redirectToRoute('app_products_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * @Route ("/printproduct/{id}", name="print_product", requirements={"id":"\d+"})
     */
    public function exportProductPDF($id, ProductsRepository $repo)
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $product = $repo->find($id);

        $dompdf->setOptions($pdfOptions);
        $dompdf->output();


        $html = $this->renderView(
            'product/print.html.twig',
            [
                'product' => $product
            ]
        );

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        $fn = sprintf('product%s.pdf', date('c'));
        // Output the generated PDF to Browser (force download)
        $dompdf->stream($fn, [
            "Attachment" => true
        ]);
    }


    /**
     * @Route ("/printallproducts", name="print_products", requirements={"id":"\d+"})
     */
    public function exportAllProductsPDF(ProductsRepository $repo)
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $products = $repo->findAll();

        $dompdf->setOptions($pdfOptions);
        $dompdf->output();


        $html = $this->renderView(
            'products/print.html.twig',
            [
                'products' => $products
            ]
        );

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        $fn = sprintf('allproducts%s.pdf', date('c'));
        // Output the generated PDF to Browser (force download)
        $dompdf->stream($fn, [
            "Attachment" => true
        ]);
    }
}
