<?php

namespace App\Entity;

use App\Repository\AnnoncesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;

#[ORM\Entity(repositoryClass: AnnoncesRepository::class)]
#[HasLifecycleCallbacks]
/**
 * Summary of Annonces
 */
class Annonces
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $imgUrl = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateModification = null;

    #[ORM\OneToMany(mappedBy: 'annonces', targetEntity: Commentaire::class)]
    private Collection $joinCommentaire;

    #[ORM\ManyToOne(inversedBy: 'joinAnnonces')]
    private ?User $joinUser = null;

    #[ORM\Column(type: "integer", options: ["default" => 0])]
    private int $rating = 0;


    public function __construct()
    {
        $this->joinCommentaire = new ArrayCollection();
    }

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

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImgUrl(): ?string
    {
        return $this->imgUrl;
    }

    public function setImgUrl(string $imgUrl): self
    {
        $this->imgUrl = $imgUrl;

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

    /**
     * @ORM\PrePersist
     */
    public function setDatesOnCreate(): void
    {
        $this->dateCreation = new \DateTime();
        $this->dateModification = new \DateTime();
    }

    
    #[ORM\PrePersist]
    public function setDateOnUpdate(): void
    {
        $this->dateCreation = new \DateTime();
        $this->dateModification = new \DateTime();
    }
    #[ORM\PreUpdate]
    public function setUpdatedAt(): self
    {
        $this->dateModification = new \DateTime();

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getJoinCommentaire(): Collection
    {
        return $this->joinCommentaire;
    }

    public function addJoinCommentaire(Commentaire $joinCommentaire): self
    {
        if (!$this->joinCommentaire->contains($joinCommentaire)) {
            $this->joinCommentaire->add($joinCommentaire);
            $joinCommentaire->setAnnonces($this);
        }

        return $this;
    }

    public function removeJoinCommentaire(Commentaire $joinCommentaire): self
    {
        if ($this->joinCommentaire->removeElement($joinCommentaire)) {
            // set the owning side to null (unless already changed)
            if ($joinCommentaire->getAnnonces() === $this) {
                $joinCommentaire->setAnnonces(null);
            }
        }

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }
}