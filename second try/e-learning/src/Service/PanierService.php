<?php

namespace App\Service;
use Symfony\Component\Security\Core\Security;
use App\Entity\Panier;
use App\Entity\Produit;
use App\Entity\ProduitChoisi;
use Doctrine\ORM\EntityManagerInterface;



class PanierService
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function getCurrentPanier(): Panier
    {
        $user = $this->security->getUser();
        $panier = $user->getPanier();

        if (!$panier) {
            $panier = new Panier();
            $panier->setUser($user);
            $this->em->persist($panier);
            $this->em->flush();
        }

        return $panier;
    }

    public function addProduitToPanier(Produit $produit, int $quantity = 1): void
    {
        $panier = $this->getCurrentPanier();

        // Check if the product is already in the cart
        foreach ($panier->getProduitChoisis() as $produitChoisi) {
            if ($produitChoisi->getProduit()->getId() === $produit->getId()) {
                $produitChoisi->increaseQuantity($quantity);
                $this->em->flush();
                return;
            }
        }

        // If not in the cart, add it
        $produitChoisi = new ProduitChoisi();
        $produitChoisi->setProduit($produit);
        $produitChoisi->setPanier($panier);
        $produitChoisi->setQuantity($quantity);

        $this->em->persist($produitChoisi);
        $this->em->flush();
    }

    public function removeProduitFromPanier(Produit $produit): void
    {
        $panier = $this->getCurrentPanier();

        foreach ($panier->getProduitChoisis() as $produitChoisi) {
            if ($produitChoisi->getProduit()->getId() === $produit->getId()) {
                $panier->removeProduitChoisi($produitChoisi);
                $this->em->remove($produitChoisi);
                $this->em->flush();
                return;
            }
        }
    }

    public function calculateTotal(): float
    {
        $total = 0.0;
        $panier = $this->getCurrentPanier();

        foreach ($panier->getProduitChoisis() as $produitChoisi) {
            $total += $produitChoisi->getProduit()->getPrice() * $produitChoisi->getQuantity();
        }

        return $total;
    }



}
