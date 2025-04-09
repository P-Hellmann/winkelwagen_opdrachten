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

    /**
     * Remove item from shoppingcart.
     *
     * @return void
     */
    public function removeProductFromCart(int $product_id):void
    {
        if ($this->shoppingCart!=null) {
            foreach ($this->shoppingCart as $key=>$orderLine) {
                if ($orderLine->getProduct()->getId() == $product_id) {
                    unset($this->shoppingCart[$key]);
                }
            }
        }
        $this->serializeShoppingCart();
    }

    /**
     * Add Product in shoppingcart in session
     *
     * @return void
     */
    public function addProductToCart(int $product_id):void
    {
        $exist = false;
        // Search for orderline with product_id => inc quantity
        if ($this->shoppingCart!=null) {
            foreach ($this->shoppingCart as $orderLine) {
                if ($orderLine->getProduct()->getId() == $product_id) {
                    $orderLine->setQuantity($orderLine->getQuantity() + 1);
                    $exist = true;
                }
            }
        }
        if (!$exist) {
            $newOrderLine = new OrderLine();
            $newOrderLine->setQuantity(1);
            $newOrderLine->setProduct($this->productRepository->find($product_id));
            $this->shoppingCart[] = $newOrderLine;
        }
        $this->serializeShoppingCart();
    }

    /**
     * Get amount of products in shoppingcart.
     *
     * @return void
     */
    public function GetNumberOfProductInCart()
    {
        
    }

    private function serializeShoppingCart():void
    {
        $cart = [];
        if ($this->shoppingCart!=null) {
            foreach ($this->shoppingCart as $orderLine) {
                $cart[] = serialize($orderLine);
            }
        }
        $this->session->set('cart', $cart);
    }
}