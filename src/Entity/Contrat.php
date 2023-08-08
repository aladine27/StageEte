<?php

// src/Entity/Contrat.php

namespace App\Entity;

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
    private $Type_Facture;

    #[ORM\Column(type:"datetime")]
    private $Date_limite_paiement;

    #[ORM\Column(type:"float")]
    private $NetAPayer;

    // Getters and Setters
    // ...
     public function getId(): ?int
    {
        return $this->id;
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

    public function getTypeFacture(): ?string
    {
        return $this->Type_Facture;
    }

    public function setTypeFacture(string $Type_Facture): self
    {
        $this->Type_Facture = $Type_Facture;
        return $this;
    }

    public function getDateLimitePaiement(): ?\DateTimeInterface
    {
        return $this->Date_limite_paiement;
    }

    public function setDateLimitePaiement(\DateTimeInterface $Date_limite_paiement): self
    {
        $this->Date_limite_paiement = $Date_limite_paiement;
        return $this;
    }

    public function getNetAPayer(): ?float
    {
        return $this->NetAPayer;
    }

    public function setNetAPayer(float $NetAPayer): self
    {
        $this->NetAPayer = $NetAPayer;
        return $this;
    }
}
