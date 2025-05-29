<?php

namespace App\Entity;

use App\Repository\ProduitChoisiRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitChoisiRepository::class)]
class ProduitChoisi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Formation $formation = null;

    #[ORM\Column]
    private ?\DateTime $dateEtTempsAjout = null;

    #[ORM\ManyToOne(inversedBy: 'produitChoisis')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Panier $panier = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFormation(): ?Formation
    {
        return $this->formation;
    }

    public function setFormation(?Formation $formation): static
    {
        $this->formation = $formation;

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
