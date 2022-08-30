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
    public function index(RequestStack $rs, ProductRepository $repo): Response
    {
        // On recupère la SESSION
        $session = $rs->getSession();
        $cart = $session->get('cart', []);

        // Quantité totale du panier
        $quantityPanier = 0;

        // on crée un nouveau tableau qui contiendra des objets Product et les quantités de chauque OBJET
        $cartWithData = [];

        // $cartWithData est un tableau multidimensionnel:
        // Pour chaque ID qui se trouve dans le panier, nous allons créer un nouveau tableau dans $cartWithData qui contiendra 2 cases:
        // Product, Quantity
        foreach ($cart as $id => $quantity) {
            // On créer une nouvelle case dans le tableau $cartWithData
            $cartWithData[] = [
                'product' => $repo->find($id),
                'quantity' => $quantity
            ];
            $quantityPanier += $quantity;
        }

        $session->set('totalQuantity', $quantityPanier);

        // Pour chaque produit dans mon panier, j erécupère le sous total
        $totalPanier = 0;
        foreach ($cartWithData as $item) {
            $totalItem = $item['product']->getPrice() * $item['quantity'];
            $totalPanier += $totalItem;
        }

        $session->set('totalPanier', $totalPanier);


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
