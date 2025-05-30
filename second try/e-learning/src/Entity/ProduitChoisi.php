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


    #[ORM\Column]
    private ?\DateTime $dateEtTempsAjout = null;

    #[ORM\ManyToOne(inversedBy: 'produitChoisis')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Panier $panier = null;

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
}
