<?php

namespace App\Entity;

use App\Repository\FormateurRequestRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormateurRequestRepository::class)]
class FormateurRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $accepted = false;

    #[ORM\Column]
    private ?bool $treated = false;

    #[ORM\Column]
    private ?\DateTimeImmutable $requested_at = null;

    #[ORM\ManyToOne(inversedBy: 'formateurRequests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function __construct()
    {
        $this->accepted = false;
        $this->treated = false;
        $this->requested_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isAccepted(): ?bool
    {
        return $this->accepted;
    }

    public function setAccepted(bool $accepted): static
    {
        $this->accepted = $accepted;
        return $this;
    }

    public function isTreated(): ?bool
    {
        return $this->treated;
    }

    public function setTreated(bool $treated): static
    {
        $this->treated = $treated;
        return $this;
    }

    public function getRequestedAt(): ?\DateTimeImmutable
    {
        return $this->requested_at;
    }

    public function setRequestedAt(\DateTimeImmutable $requested_at): static
    {
        $this->requested_at = $requested_at;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }
}
