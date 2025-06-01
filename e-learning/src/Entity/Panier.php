<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PanierRepository::class)]
class Panier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'panier', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    #[ORM\OneToOne(mappedBy: 'panier', cascade: ['persist', 'remove'])]
    private ?Commande $commande = null;

    /**
     * @var Collection<int, ProduitChoisi>
     */
    #[ORM\OneToMany(mappedBy: 'panier', targetEntity: ProduitChoisi::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $produitChoisis;

    public function __construct()
    {
        $this->produitChoisis = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(Commande $commande): static
    {
        // set the owning side of the relation if necessary
        if ($commande->getPanier() !== $this) {
            $commande->setPanier($this);
        }

        $this->commande = $commande;

        return $this;
    }

    /**
     * @return Collection<int, ProduitChoisi>
     */
    public function getProduitChoisis(): Collection
    {
        return $this->produitChoisis;
    }

    public function addProduitChoisi(ProduitChoisi $produitChoisi): static
    {
        if (!$this->produitChoisis->contains($produitChoisi)) {
            $this->produitChoisis->add($produitChoisi);
            $produitChoisi->setPanier($this);
        }

        return $this;
    }

    public function removeProduitChoisi(ProduitChoisi $produitChoisi): static
    {
        if ($this->produitChoisis->removeElement($produitChoisi)) {
            // set the owning side to null (unless already changed)
            if ($produitChoisi->getPanier() === $this) {
                $produitChoisi->setPanier(null);
            }
        }

        return $this;
    }

    // Calculate the total price of the cart
   public function getTotal(): float
    {
        $total = 0.0;
        foreach ($this->produitChoisis as $produitChoisi) {
            $total += $produitChoisi->getTotalPrice();
        }
        return $total;
    }

    // Check if the product is already in the cart (to update quantity, for example)
    public function hasProduit(Produit $produit): ?ProduitChoisi
    {
        foreach ($this->produitChoisis as $produitChoisi) {
            if ($produitChoisi->getProduit()->getId() === $produit->getId()) {
                return $produitChoisi;
            }
        }
        return null;
    }
   // pour garder l'historique des paniers , c'est comme si on fait une capture du panier à chaque commande
    public function __clone(): void
    {
        $this->id = null;

        $clonedProduits = new ArrayCollection();

        foreach ($this->getProduitChoisis() as $produitChoisi) {
            $cloned = clone $produitChoisi;
            $cloned->setPanier($this); 
            $clonedProduits->add($cloned);
        }

        $this->produitChoisis = $clonedProduits;
    }

    public function clear(): void
    {
        foreach ($this->getProduitChoisis() as $produitChoisi) {
            $this->removeProduitChoisi($produitChoisi);
        }
    }

    public function deepClone(): Panier
    {
        $newPanier = new self();
        foreach ($this->getProduitChoisis() as $produitChoisi) {
            $newProduitChoisi = new ProduitChoisi();
            $newProduitChoisi->setProduit($produitChoisi->getProduit());
            $newProduitChoisi->setQuantity($produitChoisi->getQuantity());
            $newProduitChoisi->setDateEtTempsAjout(clone $produitChoisi->getDateEtTempsAjout());
            $newProduitChoisi->setPanier($newPanier);
            $newPanier->addProduitChoisi($newProduitChoisi);
        }

        return $newPanier;
    }



    


}
