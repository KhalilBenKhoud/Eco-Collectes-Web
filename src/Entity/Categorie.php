<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "categorie Est Obligatoire")]

    private ?string $ref_categorie = null;

    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: Article::class)]
    private Collection $ref;

    public function __construct()
    {
        $this->ref = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRefCategorie(): ?string
    {
        return $this->ref_categorie;
    }

    public function setRefCategorie(string $ref_categorie): self
    {
        $this->ref_categorie = $ref_categorie;

        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getRef(): Collection
    {
        return $this->ref;
    }

    public function addRef(Article $ref): self
    {
        if (!$this->ref->contains($ref)) {
            $this->ref->add($ref);
            $ref->setCategorie($this);
        }

        return $this;
    }

    public function removeRef(Article $ref): self
    {
        if ($this->ref->removeElement($ref)) {
            // set the owning side to null (unless already changed)
            if ($ref->getCategorie() === $this) {
                $ref->setCategorie(null);
            }
        }

        return $this;
    }
    public function __toString()
    {
        return (string) $this->ref_categorie;
    }
}