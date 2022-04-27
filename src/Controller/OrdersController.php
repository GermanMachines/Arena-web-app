<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Entity\Users;
use App\Form\OrdersType;
use App\Repository\OrdersRepository;
use App\Repository\ProductsRepository;
use App\Repository\UsersRepository;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

use function PHPUnit\Framework\lessThan;

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

        $pieChart = new PieChart();

        $ordersData = $this->getDoctrine()->getRepository(Orders::class)->findAll();

        $lessthan500 = 0;
        $between = 0;
        $morethan1000 = 0;

        foreach ($ordersData as $o) {
            $totalPrice = $o->getProductQty() * $o->getIdproduct()->getPrice();
            if ($totalPrice <= 500) {
                $lessthan500++;
            } else if ($totalPrice > 500 && $totalPrice <= 1000) {
                $between++;
            } else {
                $morethan1000++;
            }
        }

        $charts = array(
            ['Orders', 'Number per Price'],
            ['P < 500 TND', $lessthan500],
            ['500 TND < P < 1000 TND', $between],
            ['P > 1000 TND', $morethan1000],
        );

        $pieChart->getData()->setArrayToDataTable(
            $charts
        );

        // dd($pieChart);

        $pieChart->getOptions()->setTitle('Orders Number per Price');
        $pieChart->getOptions()->setHeight(400);
        $pieChart->getOptions()->setWidth(400);
        $pieChart->getOptions()->getTitleTextStyle()->setColor('#07600');
        $pieChart->getOptions()->getTitleTextStyle()->setFontSize(25);


        return $this->render('orders/index.html.twig', [
            'orders' => $orders,
            'piechart' => $pieChart
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

    function addOrder(SessionInterface $session, ProductsRepository $repo, OrdersRepository $ordersRepository, UsersRepository $userRepo)
    {
        $cart = $session->get("cart", []);
        $dataCart = [];
        $total = 0;
        $products = [];

        foreach ($cart as $id => $qty) {
            $product = $repo->find($id);
            if ($qty > 0) {
                $dataCart[] = [
                    "product" => $product,
                    "qty" => $qty
                ];
                $total += $product->getPrice() * $qty;

                $user = $userRepo->find(1);
                $order = new Orders();
                // $order->setTotal($product->getPrice() * $qty);
                $order->setIdproduct($product);
                $order->setIduser($user);
                // $order->setIduser(null);
                $order->setProductqty($qty);

                array_push($products, $order);
            }
        }

        // dd($dataCart);
        $s = 0;

        for ($i = 0; $i < count($dataCart); $i++) {
            $s = $s + 1;
        }
        return $this->render('order/order.html.twig', ['s', 'dataCart' => $dataCart, 'total' => $total]);
    }


    /**
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     * @Route ("/confirmorder", name="confirm_order", methods={"GET","POST"})
     */

    function confirmOrder(SessionInterface $session, ProductsRepository $repo, OrdersRepository $ordersRepository, UsersRepository $userRepo)
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
                $total += $product->getPrice() * $qty;

                $user = $userRepo->find(1);
                $order = new Orders();
                // $order->setTotal($product->getPrice() * $qty);
                $order->setNum(random_int(1000, 9999));
                $order->setIdproduct($product);
                $order->setIduser($user);
                // $order->setIduser(null);
                $order->setProductqty($qty);

                $ordersRepository->add($order);

                array_push($products, $order);
            }
        }

        $cart = $session->set("cart", []);
        $dataCart = $cart;


        return $this->redirectToRoute('displaycart', [], Response::HTTP_SEE_OTHER);

        // dd($dataCart);
        $s = 0;

        for ($i = 0; $i < count($dataCart); $i++) {
            $s = $s + 1;
        }
        // dd($s);
        return $this->render('order/order.html.twig', ['s', 'dataCart' => $dataCart, 'total' => $total, 'cart' => $cart]);
    }
}
