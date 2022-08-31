<?php
namespace App\Service;

use App\Repository\ProductRepository;
use Symfony\Component\BrowserKit\Response;
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
    
    public function decrement($id) {
        // On va créer une SESSION grâce à la classe RequestStack
        $session = $this->rs->getSession();

        // On récupère l'attribut de SESSION cart s'il existe, sinon , on récupère un tableau vide
        $cart = $session->get('cart', []);
        // Le tableau cart contient les quantités commandées des produits

        // Si le produit existe déjà dans le panier, on incrémente la quantité
        if (!empty($cart[$id])) {
            if ($cart[$id] > 1){
                $cart[$id]--;
            } else {
                unset($cart[$id]);
            }
        }

        // Je sauvegarde l'état de monm panier à l'attribut de session 'cart'
        $session->set('cart', $cart);
    }

    public function empty()
    {
        $session = $this->rs->getSession();
        $session->remove('cart');
    }

    public function getCartWithData()
    {
        // On recupère la SESSION
        $session = $this->rs->getSession();
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
                'product' => $this->repo->find($id),
                'quantity' => $quantity
            ];
            $quantityPanier += $quantity;
        }

        $session->set('totalQuantity', $quantityPanier);

        return $cartWithData;
    }

    public function getTotalPanier()
    {
        $session = $this->rs->getSession();
        $cartWithData = $this->getCartWithData();

        // Pour chaque produit dans mon panier, j erécupère le sous total
        $totalPanier = 0;
        foreach ($cartWithData as $item) {
            $totalItem = $item['product']->getPrice() * $item['quantity'];
            $totalPanier += $totalItem;
        }

        $session->set('totalPanier', $totalPanier);

        return $totalPanier;
    }
}