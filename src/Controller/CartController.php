<?php

namespace App\Controller;

use App\Repository\ProductRepository;
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
        }

        // Pour chaque produit dans mon panier, j erécupère le sous total
        $totalPanier = 0;
        $quantityPanier = 0;
        foreach($cartWithData as $item) {
            $totalItem = $item['product']->getPrice() * $item['quantity'];
            $totalPanier += $totalItem;
            $quantityPanier += $item['quantity'];
        }

 
        return $this->render('cart/index.html.twig', [
            'items' => $cartWithData,
            'totalPanier' => $totalPanier,
            'quantityPanier' => $quantityPanier
        ]);
    }

    /**
     * @Route("/cart/add/{id}", name="cart_add")
     */
    public function add($id, RequestStack $rs): Response
    {
        // On va créer une SESSION grâce à la classe RequestStack
        $session = $rs->getSession();

        // On récupère l'attribut de SESSION cart s'il existe, sinon , on récupère un tableau vide
        $cart = $session->get('cart', []);
        // Le tableau cart contient les quantités commandées des produits

        // Si le produit existe déjà dans le panier, on incrémente la quantité
        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            // l'index du tableau cart est l'id du produit
            $cart[$id] = 1;
        }
        // Je sauvegarde l'état de monm panier à l'attribut de session 'cart'
        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart');
    }
}
