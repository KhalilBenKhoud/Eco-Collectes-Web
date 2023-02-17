<?php

namespace App\Entity;

use App\Repository\CollectRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CollectRepository::class)]
class Collect
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $NomDonneur = null;

    #[ORM\Column(length: 255)]
    private ?string $Localisation = null;

    #[ORM\Column]
    private ?int $Quantite = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomDonneur(): ?string
    {
        return $this->NomDonneur;
    }

    public function setNomDonneur(string $NomDonneur): self
    {
        $this->NomDonneur = $NomDonneur;

        return $this;
    }

    public function getLocalisation(): ?string
    {
        return $this->Localisation;
    }

    public function setLocalisation(string $Localisation): self
    {
        $this->Localisation = $Localisation;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->Quantite;
    }

    public function setQuantite(int $Quantite): self
    {
        $this->Quantite = $Quantite;

        return $this;
    }
}
