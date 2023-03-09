<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
#[HasLifecycleCallbacks]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: 'text')]
    private ?string $contenu = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateModification = null;

    #[ORM\ManyToOne(inversedBy: 'joinCommentaire')]
    private ?Annonces $annonces = null;

    #[ORM\ManyToOne(inversedBy: 'joinCommentaire')]
    private ?User $joinUser = null;

    /**
     * Summary of getJoinUser
     * @return User|null
     */
    public function getJoinUser(): ?User
    {
        return $this->joinUser;
    }

    public function setJoinUser(?User $user): self
    {
        $this->joinUser = $user;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function gettitre(): ?string
    {
        return $this->titre;
    }

    public function settitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getDateModification(): ?\DateTimeInterface
    {
        return $this->dateModification;
    }

    public function setDateModification(\DateTimeInterface $dateModification): self
    {
        $this->dateModification = $dateModification;

        return $this;
    }

    #[ORM\PrePersist, ORM\PreUpdate]
    public function setDates(): void
    {
        $this->dateModification = new \DateTime();

        if ($this->dateCreation === null) {
            $this->dateCreation = new \DateTime();
        }
    }

    public function getAnnonces(): ?Annonces
    {
        return $this->annonces;
    }

    public function setAnnonces(?Annonces $annonces): self
    {
        $this->annonces = $annonces;

        return $this;
    }
}
