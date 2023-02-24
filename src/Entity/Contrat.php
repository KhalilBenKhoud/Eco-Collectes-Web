<?php

namespace App\Entity;

use App\Repository\ContratRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ContratRepository::class)]
class Contrat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"description is requierd")]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message:"date_debut is requierd")]
    #[Assert\GreaterThanOrEqual("today",message:"La date du debut doit etre superieure à la date actuelle")]
    private ?\DateTimeInterface $date_debut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message:"date_fin is requierd")]
    #[Assert\GreaterThanOrEqual(propertyPath:"dateDebut",message:"La date du fin doit être supérieure à la date début")]
    private ?\DateTimeInterface $date_fin = null;

    #[ORM\Column(length: 255)]
    private ?string $statut_contrat = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"type-contrat is requierd")]
    private ?string $type_contrat = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"montant is requierd")]
    private ?float $montant = null;

    #[ORM\ManyToOne(inversedBy: 'contrats')]
    private ?Entreprise $enterprise = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?User $collecteur = null;

    #[ORM\OneToMany(mappedBy: 'contrat', targetEntity: Invitation::class)]
    private Collection $invitations;

    public function __construct()
    {
        $this->invitations = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    /**
     * @param \DateTimeInterface|null $date_debut
     */
    public function setDateDebut(?\DateTimeInterface $date_debut): void
    {
        $this->date_debut = $date_debut;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    /**
     * @param \DateTimeInterface|null $date_fin
     */
    public function setDateFin(?\DateTimeInterface $date_fin): void
    {
        $this->date_fin = $date_fin;
    }

    /**
     * @return string|null
     */
    public function getStatutContrat(): ?string
    {
        return $this->statut_contrat;
    }

    /**
     * @param string|null $statut_contrat
     */
    public function setStatutContrat(?string $statut_contrat): void
    {
        $this->statut_contrat = $statut_contrat;
    }

    /**
     * @return string|null
     */
    public function getTypeContrat(): ?string
    {
        return $this->type_contrat;
    }

    /**
     * @param string|null $type_contrat
     */
    public function setTypeContrat(?string $type_contrat): void
    {
        $this->type_contrat = $type_contrat;
    }

    /**
     * @return float|null
     */
    public function getMontant(): ?float
    {
        return $this->montant;
    }

    /**
     * @param float|null $montant
     */
    public function setMontant(?float $montant): void
    {
        $this->montant = $montant;
    }



    public function getEnterprise(): ?Entreprise
    {
        return $this->enterprise;
    }

    public function setEnterprise(?Entreprise $enterprise): self
    {
        $this->enterprise = $enterprise;

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

    /**
     * @return Collection<int, Invitation>
     */
    public function getInvitations(): Collection
    {
        return $this->invitations;
    }

    public function addInvitation(Invitation $invitation): self
    {
        if (!$this->invitations->contains($invitation)) {
            $this->invitations->add($invitation);
            $invitation->setContrat($this);
        }

        return $this;
    }

    public function removeInvitation(Invitation $invitation): self
    {
        if ($this->invitations->removeElement($invitation)) {
            // set the owning side to null (unless already changed)
            if ($invitation->getContrat() === $this) {
                $invitation->setContrat(null);
            }
        }

        return $this;
    }
}
