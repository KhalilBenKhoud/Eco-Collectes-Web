<?php

namespace App\Entity;

use DateTimeInterface;
use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("articles")]

    private ?int $id = null;
    #[ORM\Column(length: 255)]

    #[Assert\NotBlank(message: "Le Titre Est Obligatoire")]
    #[Assert\Length(
    min: 2,
    max: 20,
    minMessage: 'Titre must be at least {{ limit }} characters long',
    maxMessage: 'Titre first name cannot be longer than {{ limit }} characters',
    )]
    #[Groups("articles")]


    private ?string $titre = null;
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "IL Faut écrire qlq chose")]
    #[Assert\Length(
    min: 2,
    max: 50,
    minMessage: 'description must be at least {{ limit }} characters long',
    maxMessage: 'description  cannot be longer than {{ limit }} characters',
    )]
    #[Groups("articles")]

    private ?string $contenu = null;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Commentaire::class)]

    private Collection $Relation;
    #[ORM\ManyToOne(inversedBy: 'ref')]
    #[Groups("articles")]

    private ?Categorie $categorie = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups("articles")]

    private ?\DateTimeInterface $date_de_creation = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("articles")]

    private ?string $photo = null;

    public function __construct()
    {
        $this->Relation = new ArrayCollection();
        $this->date_de_creation = new \DateTime();
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


    public function __toString()
    {
        return (string) $this->contenu;
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

    /**
     * @return Collection<int, Commentaire>
     */
    public function getRelation(): Collection
    {
        return $this->Relation;
    }

    public function addRelation(Commentaire $relation): self
    {
        if (!$this->Relation->contains($relation)) {
            $this->Relation->add($relation);
            $relation->setArticle($this);
        }

        return $this;
    }

    public function removeRelation(Commentaire $relation): self
    {
        if ($this->Relation->removeElement($relation)) {
            // set the owning side to null (unless already changed)
            if ($relation->getArticle() === $this) {
                $relation->setArticle(null);
            }
        }

        return $this;
    }


    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getDateDeCreation(): ?\DateTimeInterface
    {
        return $this->date_de_creation;
    }

    public function setDateDeCreation(\DateTimeInterface $date_de_creation): self
    {
        $this->date_de_creation = $date_de_creation;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }
}