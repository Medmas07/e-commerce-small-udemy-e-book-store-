<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Service\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\SecurityBundle\Security;


final class PanierController extends AbstractController
{
    #[Route('/panier', name: 'panier_view')]
    public function index(PanierService $panierService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $panier = $panierService->getCurrentPanier();
        $total = $panierService->calculateTotal();

        return $this->render('panier/index.html.twig', [
            'panier' => $panier,
            'total' => $total,
        ]);
    }


    #[Route('/ajouter/{id}', name: 'panier_add')]
    public function add(Produit $produit, Request $request, PanierService $panierService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $quantity = $request->query->getInt('quantity', 1);
        $panierService->addProduitToPanier($produit, $quantity);

        $this->addFlash('success', 'Produit ajouté au panier.');

        return $this->redirectToRoute('panier_view');
    }

    #[Route('/panier/retirer/{id}', name: 'panier_remove')]
    public function remove(Produit $produit, PanierService $panierService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $panierService->removeProduitFromPanier($produit);

        $this->addFlash('info', 'Produit retiré du panier.');

        return $this->redirectToRoute('panier_view');
    }
}
