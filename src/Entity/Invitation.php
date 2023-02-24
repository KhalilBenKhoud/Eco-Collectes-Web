<?php

namespace App\Entity;

use App\Repository\InvitationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvitationRepository::class)]
class Invitation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'invitations')]
    private ?Contrat $contrat = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_invitation = null;


    #[ORM\ManyToOne(inversedBy: 'invitations')]
    private ?User $collecteur = null;

    #[ORM\Column(length: 255)]
    private ?string $statut_invitation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContrat(): ?Contrat
    {
        return $this->contrat;
    }

    public function setContrat(?Contrat $contrat): self
    {
        $this->contrat = $contrat;

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

    public function getDateInvitation(): ?\DateTimeInterface
    {
        return $this->date_invitation;
    }

    public function setDateInvitation(\DateTimeInterface $date_invitation): self
    {
        $this->date_invitation = $date_invitation;

        return $this;
    }


    public function getCollecteur(): ?User
    {
        return $this->collecteur;
    }

    public function setCollecteur(?User $collecteur): self
    {
        $this->collecteur = $collecteur;

        return $this;
    }

    public function getStatutInvitation(): ?string
    {
        return $this->statut_invitation;
    }

    public function setStatutInvitation(string $statut_invitation): self
    {
        $this->statut_invitation = $statut_invitation;

        return $this;
    }
}
