<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderLine;
use App\Form\OrderType;
use App\Storage\CartSessionStorage;
use Doctrine\ORM\EntityManagerInterface;
use http\Header;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(CartSessionStorage $cartSessionStorage, Request $request, EntityManagerInterface $entityManager): Response
    {
        $shoppingCart = $cartSessionStorage->getShoppingCart();

        $Order = new Order;
        $form = $this->createForm(OrderType::class, $Order);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $order = $form->getData();
            foreach ($shoppingCart as $orderLine) {
                $order->addOrderLine($orderLine);
                $entityManager->persist($orderLine);
            }
            $entityManager->persist($order);
            $entityManager->flush();
            $this->addFlash("success", "Order successfully placed!");
            return $this->redirectToRoute('app_home');
        }

        return $this->render('cart/index.html.twig', [
            'shoppingCart' => $shoppingCart,
            'form' => $form,
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
