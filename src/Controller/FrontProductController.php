<?php

namespace App\Controller;

use App\Entity\Products;
use App\Repository\ProductsRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FrontProductController extends AbstractController
{
    /**
     * @Route("/front/product", name="app_front_product")
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

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }
    
    /**
     * @Route("/{id}", name="app_product_show", methods={"GET"}, requirements={"id":"\d+"})
     */
    public function show(Products $product): Response
    {
        return $this->render('product/product.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/displaycart", name="displaycart")
     */
    public function displayCart(SessionInterface $session, ProductsRepository $repo)
    {
        $cart = $session->get("cart", []);

        // On "fabrique" les données
        $dataCart = [];
        $total = 0;
        $s = 0;
        foreach ($cart as $id => $qty) {
            $product = $repo->find($id);
            $dataCart[] = [
                "product" => $product,
                "qty" => $qty
            ];
            $total += $product->getPrice() * $qty;
        }
        for ($i = 0; $i < count($dataCart); $i++) {
            $s = $s + 1;
        }

        return $this->render('/cart/show_cart.html.twig', compact("dataCart", "total", "s"));
    }
    /**
     * @Route("/cart/add/{id}", name="add_cart", requirements={"id":"\d+"})
     */
    public function add(Products $product, SessionInterface $session)
    {
        // On récupère le panier actuel
        $cart = $session->get("cart", []);
        $id = $product->getId();

        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        // On sauvegarde dans la session
        $session->set("cart", $cart);

        return $this->redirectToRoute("displaycart");
    }
    /**
     * @Route("/cart/addcart/{id}", name="add_cart", requirements={"id":"\d+"})
     */
    public function add_cart(Products $product, SessionInterface $session)
    {
        // On récupère le panier actuel
        $cart = $session->get("cart", []);
        $id = $product->getId();

        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        // On sauvegarde dans la session
        $session->set("cart", $cart);

        return $this->redirectToRoute("displaycart");
    }
    /**
     * @Route("/supprimer/{id}", name="remove_cart", requirements={"id":"\d+"})
     */
    public function remove($id, SessionInterface $session)
    {
        $cart = $session->get('cart', []);

        if (empty($cart[$id])) {

            unset($cart[$id]);
        } else {
            $cart[$id]--;
        }


        $session->set('cart', $cart);

        return $this->redirectToRoute('displaycart');
    }
}
