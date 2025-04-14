<?php

namespace App\Controller;

use App\Storage\CartSessionStorage;
use http\Header;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(CartSessionStorage $cartSessionStorage): Response
    {
        $shoppingCart = $cartSessionStorage->getShoppingCart();

        return $this->render('cart/index.html.twig', [
            'shoppingCart' => $shoppingCart,
            'amount' => $cartSessionStorage->getNumberOfProductsInCart(),
            'totalPrice' => $cartSessionStorage->getTotalPrice(),
        ]);
    }

    #[Route('/cart/empty', name: 'app_cart_empty')]
    public function empty(CartSessionStorage $cartSessionStorage): Response
    {
        $cartSessionStorage->clearShoppingCart();
        $shoppingCart = $cartSessionStorage->getShoppingCart();
        return $this->redirectToRoute('app_cart');
    }
    #[Route('/cart/remove/product/{id}', name: 'app_remove_cart_product')]
    public function removeProduct(CartSessionStorage $cartSessionStorage, int $id): Response
    {
        $cartSessionStorage->removeProductFromCart($id);
        return $this->redirectToRoute('app_cart');
    }
}
