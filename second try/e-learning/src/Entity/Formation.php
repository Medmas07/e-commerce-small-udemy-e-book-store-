<?php

namespace App\Entity;

use App\Repository\FormationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormationRepository::class)]
class Formation extends Produit
{
    #[ORM\ManyToOne(inversedBy: 'formation_created')]
    private ?Formateur $formateur = null;

    #[ORM\Column]
    private ?bool $published = null;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pdfFilename;

    /**
     * @var Collection<int, Formateur>
     */
   /* #[ORM\ManyToMany(targetEntity: Formateur::class, mappedBy: 'formation_created')]
    private Collection $formateurs;*/

    public function __construct()
    {
        //$this->formateurs = new ArrayCollection();
    }

    public function getPdfFilename(): ?string
    {
        return $this->pdfFilename;
    }

    public function setPdfFilename(?string $pdfFilename): self
    {
        $this->pdfFilename = $pdfFilename;

        return $this;
    }

    public function getFormateur(): ?Formateur
    {
        return $this->formateur;
    } 

    public function setFormateur(?Formateur $formateur): static
    {
        $this->formateur = $formateur;

        return $this;
    }

    public function isPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): static
    {
        $this->published = $published;

        return $this;
    }

}
