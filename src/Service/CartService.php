<?php
namespace App\Service;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService {
    private $rs;
    private $repo;

    public function __construct(RequestStack $rs, ProductRepository $repo) {
        // Hors d'un controller, nous devons faire les injections de dépendances dans un constructeur
        $this->rs = $rs;
        $this->repo = $repo;
    }

    public function add($id){
        // On va créer une SESSION grâce à la classe RequestStack
        $session = $this->rs->getSession();

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
    }

    public function remove($id) {
        $session = $this->rs->getSession();
        $cart = $session->get('cart', []);

        // Si le produit existe dans le panier, on le supprime du tableau $cart via unset()
        if (!empty($cart[$id])) {
            unset($cart[$id]);
        }

        // On sauvegarde l'état du panier
        $session->set('cart', $cart);
    }
}