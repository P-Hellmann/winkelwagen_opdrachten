<?php

namespace App\Storage;

use App\Entity\OrderLine;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class CartSessionStorage
 * @package App\Storage
 */
class CartSessionStorage
{
    /**
     * CartSessionStorage constructor.
     *
     * @return void
     * @var OrderLine[] $ShoppingCart
     */
    private $productRepository;
    private $session;
    private $shoppingCart;

    public function __construct(RequestStack $requestStack, ProductRepository $productRepository)
    {
        $this->session = $requestStack->getSession();
        $this->productRepository = $productRepository;
        $this->deserializeShoppingCart();
//    addProductToCart(int product_id)
//    removeProductFromCart(int product_id)
//    getShoppingCart()
//    getNumberOfProductsInCart()
//    getTotalPrice()
//    clearShoppingCart()
    }

    private function deserializeShoppingCart():void
    {
        $this->shoppingCart = $this->session->get('cart');
        if ($this->shoppingCart!=null) {
            foreach ($this->shoppingCart as $orderLine) {
                $orderLine = unserialize($orderLine);
                $orderLine->setProduct($this->productRepository->find($orderLine->getProduct()->getId()));
            }
        }
    }

    
}