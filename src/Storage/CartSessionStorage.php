<?php

namespace App\Storage;

use App\Entity\OrderLine;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class CartSessionStorage
 * @package App\Storage
 */
class CartSessionStorage
{
    private ProductRepository $productRepository;
    private $session;
    /** @var OrderLine[] */
    private array $shoppingCart = [];

    public function __construct(RequestStack $requestStack, ProductRepository $productRepository)
    {
        $this->session = $requestStack->getSession();
        $this->productRepository = $productRepository;
        $this->deserializeShoppingCart();
    }

    private function deserializeShoppingCart(): void
    {
        $cartData = $this->session->get('cart', []);
        $this->shoppingCart = [];

        foreach ($cartData as $serializedOrderLine) {
            /** @var OrderLine $orderLine */
            $orderLine = unserialize($serializedOrderLine);
            $orderLine->setProduct($this->productRepository->find($orderLine->getProduct()->getId()));
            $this->shoppingCart[] = $orderLine;
        }
    }

    private function serializeShoppingCart(): void
    {
        $cart = [];

        foreach ($this->shoppingCart as $orderLine) {
            $cart[] = serialize($orderLine);
        }

        $this->session->set('cart', $cart);
    }

    public function addProductToCart(int $productId): void
    {
        $found = false;

        foreach ($this->shoppingCart as $orderLine) {
            if ($orderLine->getProduct()->getId() === $productId) {
                $orderLine->setQuantity($orderLine->getQuantity() + 1);
                $found = true;
                break;
            }
        }

        if (!$found) {
            $product = $this->productRepository->find($productId);
            if ($product !== null) {
                $newOrderLine = new OrderLine();
                $newOrderLine->setQuantity(1);
                $newOrderLine->setProduct($product);
                $this->shoppingCart[] = $newOrderLine;
            }
        }

        $this->serializeShoppingCart();
    }

    public function removeProductFromCart(int $productId): void
    {
        foreach ($this->shoppingCart as $key => $orderLine) {
            if ($orderLine->getProduct()->getId() === $productId) {
                unset($this->shoppingCart[$key]);
            }
        }

        // Reindex array after unset
        $this->shoppingCart = array_values($this->shoppingCart);
        $this->serializeShoppingCart();
    }

    public function getNumberOfProductsInCart(): int
    {
        $total = 0;

        foreach ($this->shoppingCart as $orderLine) {
            $total += $orderLine->getQuantity();
        }

        return $total;
    }

    /**
     * @return OrderLine[]
     */
    public function getShoppingCart(): array
    {
        return $this->shoppingCart;
    }

    public function getTotalPrice(): float
    {
        $total = 0.0;

        foreach ($this->shoppingCart as $orderLine) {
            $total += $orderLine->getQuantity() * $orderLine->getProduct()->getPrice();
        }

        return $total;
    }

    public function clearShoppingCart(): void
    {
        $this->shoppingCart = [];
        $this->serializeShoppingCart();
    }
}