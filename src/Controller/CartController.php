<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="app_cart")
     */
    public function index(CartService $cs): Response
    {

        $cartWithData = $cs->getCartWithData();
        $totalPanier = $cs->getTotalPanier();

        return $this->render('cart/index.html.twig', [
            'items' => $cartWithData,
            'totalPanier' => $totalPanier,
        ]);
    }

    /**
     * @Route("/cart/add/{id}", name="cart_add")
     */
    public function add($id, CartService $cs): Response
    {

        $cs->add($id);

        return $this->redirectToRoute('app_cart');
    }

    /**
     * @Route("/cart/remove/{id}", name="cart_remove")
     */
    public function remove($id, CartService $cs)
    {
        $cs->remove($id);
        return $this->redirectToRoute('app_cart');
    }

    /**
     * @Route("/cart/decrease/{id}", name="cart_decrease")
     */
    public function decrease($id, CartService $cs): Response
    {

        $cs->decrement($id);

        return $this->redirectToRoute('app_cart');
    }

    /**
     * @Route("/cart/delete", name="cart_delete")
     */
     public function delete(CartService $cs): Response
     {
        $cs->empty();

        return $this->redirectToRoute('app_cart');
     }
}
