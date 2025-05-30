<?php

namespace App\Entity;

use App\Repository\FormateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormateurRepository::class)]
class Formateur
{
    #[ORM\Id]
    #[ORM\OneToOne(inversedBy: 'formateur', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id')]
    private ?User $user = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?float $wallet = null;

    #[ORM\Column(nullable: true)]
    private ?float $totalRevenue = null;

    /**
     * @var Collection<int, Formation>
     */
    #[ORM\OneToMany(mappedBy: 'formateur', targetEntity: Formation::class)]
    private Collection $formation_created;

    public function __construct()
    {
        $this->formation_created = new ArrayCollection();
    
    }
      public function getId(): ?int
    {
        return $this->user?->getId();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getWallet(): ?float
    {
        return $this->wallet;
    }

    public function setWallet(?float $wallet): static
    {
        $this->wallet = $wallet;

        return $this;
    }

    public function getTotalRevenue(): ?float
    {
        return $this->totalRevenue;
    }

    public function setTotalRevenue(?float $totalRevenue): static
    {
        $this->totalRevenue = $totalRevenue;

        return $this;
    }

    /**
     * @return Collection<int, Formation>
     */
    public function getFormationCreated(): Collection
    {
        return $this->formation_created;
    }

    public function addFormationCreated(Formation $formationCreated): static
    {
        if (!$this->formation_created->contains($formationCreated)) {
            $this->formation_created->add($formationCreated);
        }

        return $this;
    }

    public function removeFormationCreated(Formation $formationCreated): static
    {
        $this->formation_created->removeElement($formationCreated);

        return $this;
    }
}
