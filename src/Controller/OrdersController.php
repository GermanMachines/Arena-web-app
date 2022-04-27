<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Form\OrdersType;
use App\Repository\OrdersRepository;
use App\Repository\ProductsRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/orders")
 */
class OrdersController extends AbstractController
{
    /**
     * @Route("/", name="app_orders_index", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        // Méthode findBy qui permet de récupérer les données avec des critères de filtre et de tri
        $data = $this->getDoctrine()->getRepository(Orders::class)->findBy([], ['id' => 'desc']);

        $orders = $paginator->paginate(
            $data, // Requête contenant les données à paginer (ici nos orders)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            6 // Nombre de résultats par page
        );

        return $this->render('orders/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    /**
     * @Route("/new", name="app_orders_new", methods={"GET", "POST"})
     */
    public function new(Request $request, OrdersRepository $ordersRepository): Response
    {
        $order = new Orders();
        $form = $this->createForm(OrdersType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ordersRepository->add($order);
            return $this->redirectToRoute('app_orders_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('orders/new.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_orders_show", methods={"GET"}, requirements={"id":"\d+"})
     */
    public function show(Orders $order): Response
    {
        return $this->render('orders/show.html.twig', [
            'order' => $order,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_orders_edit", methods={"GET", "POST"}, requirements={"id":"\d+"})
     */
    public function edit(Request $request, Orders $order, OrdersRepository $ordersRepository): Response
    {
        $form = $this->createForm(OrdersType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ordersRepository->add($order);
            return $this->redirectToRoute('app_orders_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('orders/edit.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_orders_delete", methods={"POST"}, requirements={"id":"\d+"})
     */
    public function delete(Request $request, Orders $order, OrdersRepository $ordersRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $order->getId(), $request->request->get('_token'))) {
            $ordersRepository->remove($order);
        }

        return $this->redirectToRoute('app_orders_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route ("/printorder/{id}", name="print_order", requirements={"id":"\d+"})
     */
    public function exportOrderPDF($id, OrdersRepository $repo)
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $order = $repo->find($id);

        $dompdf->setOptions($pdfOptions);
        $dompdf->output();


        $html = $this->renderView(
            'order/print.html.twig',
            [
                'order' => $order
            ]
        );

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        $fn = sprintf('order%s.pdf', date('c'));
        // Output the generated PDF to Browser (force download)
        $dompdf->stream($fn, [
            "Attachment" => true
        ]);
    }


    /**
     * @Route ("/printallorders", name="print_orders", requirements={"id":"\d+"})
     */
    public function exportAllOrdersPDF(OrdersRepository $repo)
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $orders = $repo->findAll();

        $dompdf->setOptions($pdfOptions);
        $dompdf->output();


        $html = $this->renderView(
            'orders/print.html.twig',
            [
                'orders' => $orders
            ]
        );

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        $fn = sprintf('allorders%s.pdf', date('c'));
        // Output the generated PDF to Browser (force download)
        $dompdf->stream($fn, [
            "Attachment" => true
        ]);
    }


    /**
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     * @Route ("/addorder", name="add_order", methods={"GET","POST"})
     */

    function addOrder(SessionInterface $session, ProductsRepository $repo)
    {
        $cart = $session->get("cart", []);
        $dataCart = [];
        $total = 0;
        $products = [];
        // $em = $this->getDoctrine()->getManager();

        foreach ($cart as $id => $qty) {
            $product = $repo->find($id);
            if ($qty > 0) {
                $dataCart[] = [
                    "product" => $product,
                    "qty" => $qty
                ];
            }
            $total += $product->getPrice() * $qty;

            $order = new Orders();
            // $order->setTotal($product->getPrice() * $qty);
            $order->setIdproduct($product);
            // $order->setIduser(null);
            $order->setProductqty($qty);

            array_push($products, $order);
        }

        $s = 0;

        for ($i = 0; $i < count($dataCart); $i++) {
            $s = $s + 1;
        }
        return $this->render('order/order.html.twig', ['s', 'dataCart' => $dataCart, 'total' => $total]);
    }
}
