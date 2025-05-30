<?php

namespace App\Entity;

use App\Repository\ProduitChoisiRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Produit;

#[ORM\Entity(repositoryClass: ProduitChoisiRepository::class)]
class ProduitChoisi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Produit::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Produit $produit = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTime $dateEtTempsAjout = null;

    #[ORM\ManyToOne(inversedBy: 'produitChoisis')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Panier $panier = null;

    #[ORM\Column(type: 'integer')]
    private int $quantity = 1;  // Default quantity to 1

    public function __construct()
    {
        $this->dateEtTempsAjout = new \DateTime();  // Set default to now on creation
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): static
    {
        $this->produit = $produit;

        return $this;
    }

    public function getDateEtTempsAjout(): ?\DateTime
    {
        return $this->dateEtTempsAjout;
    }

    public function setDateEtTempsAjout(\DateTime $dateEtTempsAjout): static
    {
        $this->dateEtTempsAjout = $dateEtTempsAjout;

        return $this;
    }

    public function getPanier(): ?Panier
    {
        return $this->panier;
    }

    public function setPanier(?Panier $panier): static
    {
        $this->panier = $panier;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        if ($quantity < 1) {
            $quantity = 1; // enforce minimum quantity 1
        }
        $this->quantity = $quantity;

        return $this;
    }

    public function increaseQuantity(int $amount = 1): static
    {
        $this->quantity += $amount;

        return $this;
    }

    public function decreaseQuantity(int $amount = 1): static
    {
        $this->quantity = max(1, $this->quantity - $amount);

        return $this;
    }
    public function getTotalPrice(): float
    {
        return $this->produit?->getPrice() * $this->quantity;
    }

    public function __clone(): void
    {
        $this->id = null;
        $this->dateEtTempsAjout = new \DateTime(); // Remettre à maintenant si tu veux archiver le moment du clonage
    }

    

    
}
