<?php

// src/Entity/Contrat.php

namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: "App\Repository\ContratRepository")]
class Contrat
{
   #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")] 
    private $id;

    #[ORM\ManyToOne(targetEntity: "App\Entity\Client", inversedBy: "contrats")]
    #[ORM\JoinColumn(nullable: false)]
    private $client;

    #[ORM\Column(type:"string", length:255)]
    private $Num_Contrat;

    #[ORM\Column(type:"string", length:255)]
    private $Periode;

    #[ORM\Column(type:"string", length:255)]
    private $ordre;
     #[ORM\Column(type:"string", length:255)]
    private $tournee;

    #[ORM\Column(type:"datetime")]
    private $Date_debut_contrat;

#[ORM\OneToMany(targetEntity: Facture::class, mappedBy: "contrat")]
    private $factures;

    public function __construct()
    {
        $this->factures = new ArrayCollection();
    }
     public function __toString(): string
    {
        return $this->Num_Contrat; // Utilisez la propriété appropriée pour l'affichage
    }

    public function getFactures(): Collection
    {
        return $this->factures;
    }

    public function addFacture(Facture $facture): self
    {
        if (!$this->factures->contains($facture)) {
            $this->factures[] = $facture;
            $facture->setContrat($this);
        }

        return $this;
    }

    public function removeFacture(Facture $facture): self
    {
        if ($this->factures->removeElement($facture)) {
            if ($facture->getContrat() === $this) {
                $facture->setContrat(null);
            }
        }

        return $this;
    }

    // Getters and Setters
    // ...
     public function getId(): ?int
    {
        return $this->id;
    }
     public function getTournee(): ?int
    {
        return $this->tournee;
    }
    public function setTournee(string $tournee): self
    {
        $this->tournee = $tournee;
        return $this;
    }


    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;
        return $this;
    }

    public function getNumContrat(): ?string
    {
        return $this->Num_Contrat;
    }

    public function setNumContrat(string $Num_Contrat): self
    {
        $this->Num_Contrat = $Num_Contrat;
        return $this;
    }

    public function getPeriode(): ?string
    {
        return $this->Periode;
    }

    public function setPeriode(string $Periode): self
    {
        $this->Periode = $Periode;
        return $this;
    }

    public function getordre(): ?string
    {
        return $this->ordre;
    }

    public function setordre(string $ordre): self
    {
        $this->ordre = $ordre;
        return $this;
    }

    public function getDatedebutcontrat(): ?\DateTimeInterface
    {
        return $this->Date_debut_contrat;
    }

    public function setDatedebutcontrat(\DateTimeInterface $Date_debut_contrat): self
    {
        $this->Date_debut_contrat = $Date_debut_contrat;
        return $this;
    }

}
