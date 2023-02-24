<?php

namespace App\Entity;

use App\Repository\EntrepriseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntrepriseRepository::class)]
class Entreprise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column]
    private ?int $numtel = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse_email = null;

    #[ORM\Column(length: 255)]
    private ?string $type_enterprise = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?User $ceo = null;

    #[ORM\OneToMany(mappedBy: 'entreprise', targetEntity: User::class)]
    private Collection $list_employees;

    #[ORM\OneToMany(mappedBy: 'enterprise', targetEntity: Contrat::class)]
    private Collection $contrats;

    public function __construct()
    {
        $this->list_employees = new ArrayCollection();
        $this->contrats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getNumtel(): ?int
    {
        return $this->numtel;
    }

    public function setNumtel(int $numtel): self
    {
        $this->numtel = $numtel;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getAdresseEmail(): ?string
    {
        return $this->adresse_email;
    }

    public function setAdresseEmail(string $adresse_email): self
    {
        $this->adresse_email = $adresse_email;

        return $this;
    }

    public function getTypeEnterprise(): ?string
    {
        return $this->type_enterprise;
    }

    public function setTypeEnterprise(string $type_enterprise): self
    {
        $this->type_enterprise = $type_enterprise;

        return $this;
    }

    public function getCeo(): ?User
    {
        return $this->ceo;
    }

    public function setCeo(?User $ceo): self
    {
        $this->ceo = $ceo;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getListEmployees(): Collection
    {
        return $this->list_employees;
    }

    public function addListEmployee(User $listEmployee): self
    {
        if (!$this->list_employees->contains($listEmployee)) {
            $this->list_employees->add($listEmployee);
            $listEmployee->setEntreprise($this);
        }

        return $this;
    }

    public function removeListEmployee(User $listEmployee): self
    {
        if ($this->list_employees->removeElement($listEmployee)) {
            // set the owning side to null (unless already changed)
            if ($listEmployee->getEntreprise() === $this) {
                $listEmployee->setEntreprise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Contrat>
     */
    public function getContrats(): Collection
    {
        return $this->contrats;
    }

    public function addContrat(Contrat $contrat): self
    {
        if (!$this->contrats->contains($contrat)) {
            $this->contrats->add($contrat);
            $contrat->setEnterprise($this);
        }

        return $this;
    }

    public function removeContrat(Contrat $contrat): self
    {
        if ($this->contrats->removeElement($contrat)) {
            // set the owning side to null (unless already changed)
            if ($contrat->getEnterprise() === $this) {
                $contrat->setEnterprise(null);
            }
        }

        return $this;
    }
}
