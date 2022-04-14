<?php

namespace App\Controller;

use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontProductController extends AbstractController
{
    /**
     * @Route("/front/product", name="app_front_product")
     */
    public function index(ProductsRepository $productsRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productsRepository->findAll(),
        ]);
    }
}
