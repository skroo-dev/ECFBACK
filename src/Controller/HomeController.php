<?php

namespace App\Controller;

use App\Entity\Produit;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ManagerRegistry $doctrine): Response
    {

        $produits = $doctrine->getRepository(Produit::class)->findBy(
            ['actif' => true],
        );



        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'produits' => $produits
        ]);
    }

    #[Route('/panier', name: 'panier')]
    public function panier(SessionInterface $session): Response
    {
        $session->get('panier');
        $session->get('panierQuantité');
        return $this->render('panier/panier.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/produit/{id}', name: 'produit')]
    #[ParamConverter('Produit', class: Produit::class)]
    public function produit(Produit $produit, Request $request, SessionInterface $session): Response
    {

        if ($request->request->get('ajout')) {
       

            $session->set('panier', $request->request->get('produit'));
            $session->set('panierQuantité', $request->request->get('quantite'));

            return $this->redirectToRoute('panier', [
                'produit' => $produit,
            ]);
        }

        return $this->render('produit/produit.html.twig', [
            'produit' => $produit,
        ]);
    }

    public function menu()
    {
        $navbar = [
            ['title' => 'Accueil', 'text' => 'Accueil', 'url' => $this->generateUrl('home')],
            ['title' => 'Mon Panier', 'text' => 'Mon Panier', 'url' => $this->generateUrl('panier')],
        ];
        return $this->render("parts/navbar.html.twig", ['navbar' => $navbar]);
    }
}
