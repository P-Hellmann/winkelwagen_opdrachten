<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Storage\CartSessionStorage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(CartSessionStorage $cartSessionStorage, ProductRepository $productRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'products' => $productRepository->findAll(),
            'amount' => $cartSessionStorage->GetNumberOfProductInCart(),
        ]);
    }

    #[Route('/product/add/{id}', name: 'app_product_add')]
    public function add(CartSessionStorage $cartSessionStorage, int $id): Response
    {
        $cartSessionStorage->addProductToCart($id);
        return $this->redirectToRoute('app_home', [

        ]);
    }

}
