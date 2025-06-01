<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Enum\OrderStatus;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastName = null;

    // #[ORM\Column]
    // private ?bool $isVerified = null;

    //#[ORM\Column(length: 255 , nullable: true)]
    //private ?string $roleType = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $formateurInfo = null;
    /**
     * @var Collection<int, Formation>
     */
    #[ORM\OneToMany(targetEntity: Formation::class, mappedBy: 'formateur')]
    private Collection $formations;

    #[ORM\Column]
    private bool $isVerified = false;

    /**
     * @var Collection<int, FormateurRequest>
     */
    #[ORM\OneToMany(targetEntity: FormateurRequest::class, mappedBy: 'user')]
    private Collection $formateurRequests;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Formateur $formateur = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Panier $panier = null;

    /**
     * @var Collection<int, Commande>
     */
    #[ORM\OneToMany(targetEntity: Commande::class, mappedBy: 'user')]
    private Collection $commandes;

    public function __construct()
    {
        $this->formations = new ArrayCollection();
        $this->formateurRequests = new ArrayCollection();
        $this->roles = ['ROLE_USER'];
        $this->commandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */

    public function setRoles(array $roles): self
    {
        // Guarantee every user has ROLE_USER
        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }

        $this->roles = array_unique($roles);

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    // public function isVerified(): ?bool
    // {
    //     return $this->isVerified;
    // }

    // public function setIsVerified(bool $isVerified): static
    // {
    //     $this->isVerified = $isVerified;

    //     return $this;
    // }
/*
    public function getRoleType(): ?string
    {
        return $this->roleType;
    }

    public function setRoleType(string $roleType): static
    {
        $this->roleType = $roleType;

        return $this;
    }
*/
    public function getFormateurInfo(): ?string
    {
        return $this->formateurInfo;
    }

    public function setFormateurInfo(string $formateurInfo): static
    {
        $this->formateurInfo = $formateurInfo;

        return $this;
    }

    /**
     * @return Collection<int, Formation>
     */
   /* public function getFormations(): Collection
    {
        return $this->formations;
    }*/

   /* public function addFormation(Formation $formation): static
    {
        if (!$this->formations->contains($formation)) {
            $this->formations->add($formation);
            $formation->setFormateur($this);
        }

        return $this;
    }*/
/*
    public function removeFormation(Formation $formation): static
    {
        if ($this->formations->removeElement($formation)) {
            // set the owning side to null (unless already changed)
            if ($formation->getFormateur() === $this) {
                $formation->setFormateur(null);
            }
        }

        return $this;
    }*/

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, FormateurRequest>
     */
    public function getFormateurRequests(): Collection
    {
        return $this->formateurRequests;
    }

    public function addFormateurRequest(FormateurRequest $formateurRequest): static
    {
        if (!$this->formateurRequests->contains($formateurRequest)) {
            $this->formateurRequests->add($formateurRequest);
            $formateurRequest->setUser($this);
        }

        return $this;
    }

    public function removeFormateurRequest(FormateurRequest $formateurRequest): static
    {
        if ($this->formateurRequests->removeElement($formateurRequest)) {
            // set the owning side to null (unless already changed)
            if ($formateurRequest->getUser() === $this) {
                $formateurRequest->setUser(null);
            }
        }

        return $this;
    }

    public function getFormateur(): ?Formateur
    {
        return $this->formateur;
    }

    public function setFormateur(Formateur $formateur): static
    {
        // set the owning side of the relation if necessary
        if ($formateur->getUser() !== $this) {
            $formateur->setUser($this);
        }

        $this->formateur = $formateur;

        return $this;
    }

    public function getPanier(): ?Panier
    {
        return $this->panier;
    }

    public function setPanier(Panier $panier): static
    {
        // set the owning side of the relation if necessary
        if ($panier->getUser() !== $this) {
            $panier->setUser($this);
        }

        $this->panier = $panier;

        return $this;
    }

    /**
     * @return Collection<int, Commande>
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): static
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->setUser($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): static
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getUser() === $this) {
                $commande->setUser(null);
            }
        }

        return $this;
    }

    public function hasPurchasedFormation(Formation $formation): bool
{
    foreach ($this->getCommandes() as $commande) {
        if ($commande->getStatut() === OrderStatus::PAID && $commande->getPanier()) {
            foreach ($commande->getPanier()->getProduitChoisis() as $produitChoisi) {
                if ($produitChoisi->getProduit() instanceof Formation && $produitChoisi->getProduit()->getId() === $formation->getId()) {
                    return true;
                }
            }
        }
    }
    return false;
}

public function hasPurchasedBook(Book $book): bool
{
    foreach ($this->getCommandes() as $commande) {
        if ($commande->getStatut() === OrderStatus::PAID && $commande->getPanier()) {
            foreach ($commande->getPanier()->getProduitChoisis() as $produitChoisi) {
                if ($produitChoisi->getProduit() instanceof Book && $produitChoisi->getProduit()->getId() === $book->getId()) {
                    return true;
                }
            }
        }
    }
    return false;
}


}
