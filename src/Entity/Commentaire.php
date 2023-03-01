<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "IL Faut écrire qlq chose")]
    private ?string $ContenuCommentaire = null;

    #[ORM\ManyToOne(inversedBy: 'Relation')]
    private ?Article $article = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_de_commentaire = null;

    public function __construct()
    {
        $this->date_de_commentaire = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenuCommentaire(): ?string
    {
        return $this->ContenuCommentaire;
    }

    public function setContenuCommentaire(string $ContenuCommentaire): self
    {
        $this->ContenuCommentaire = $ContenuCommentaire;

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }

    public function getDateDeCommentaire(): ?\DateTimeInterface
    {
        return $this->date_de_commentaire;
    }

    public function setDateDeCommentaire(\DateTimeInterface $date_de_commentaire): self
    {
        $this->date_de_commentaire = $date_de_commentaire;

        return $this;
    }
}