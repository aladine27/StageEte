<?php

namespace App\Entity;


use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: "App\Repository\FactureRepository")]
class Facture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $Num_Facture = null;

    #[ORM\Column(length: 255)]
    private ?string $Type_Fact = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $Date_Lim_Pay = null;

   #[ORM\Column(type: Types::FLOAT)]
private ?float $Net_Apayer = null;

#[ORM\Column(type: Types::FLOAT)]
private ?float $Anc_Index = null;

#[ORM\Column(type: Types::FLOAT)]
private ?float $Nouv_Inedx = null;

#[ORM\Column(type: Types::FLOAT)]
private ?float $Estimation = null;

 #[ORM\ManyToOne(targetEntity: Contrat::class, inversedBy: "factures")]
             #[ORM\JoinColumn(nullable: false)]
             private $contrat;

    #[ORM\Column(length: 255)]
    private ?string $Etat = null;

    // ... autres attributs et méthodes

    public function getContrat(): ?Contrat
    {
        return $this->contrat;
    }

    public function setContrat(?Contrat $contrat): self
    {
        $this->contrat = $contrat;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumFacture(): ?int
    {
        return $this->Num_Facture;
    }

    public function setNumFacture(int $Num_Facture): static
    {
        $this->Num_Facture = $Num_Facture;

        return $this;
    }

    public function getTypeFact(): ?string
    {
        return $this->Type_Fact;
    }

    public function setTypeFact(string $Type_Fact): static
    {
        $this->Type_Fact = $Type_Fact;

        return $this;
    }

    public function getDateLimPay(): ?\DateTimeInterface
    {
        return $this->Date_Lim_Pay;
    }

    public function setDateLimPay(\DateTimeInterface $Date_Lim_Pay): static
    {
        $this->Date_Lim_Pay = $Date_Lim_Pay;

        return $this;
    }

    public function getNetApayer(): ?float
{
    return $this->Net_Apayer;
}

public function setNetApayer(?float $Net_Apayer): static
{
    $this->Net_Apayer = $Net_Apayer;

    return $this;
}

public function getAncIndex(): ?float
{
    return $this->Anc_Index;
}

public function setAncIndex(?float $Anc_Index): static
{
    $this->Anc_Index = $Anc_Index;

    return $this;
}

public function getNouvInedx(): ?float
{
    return $this->Nouv_Inedx;
}

public function setNouvInedx(?float $Nouv_Inedx): static
{
    $this->Nouv_Inedx = $Nouv_Inedx;

    return $this;
}

public function getEstimation(): ?float
{
    return $this->Estimation;
}

public function setEstimation(?float $Estimation): static
{
    $this->Estimation = $Estimation;

    return $this;
}

public function getEtat(): ?string
{
    return $this->Etat;
}

public function setEtat(string $Etat): static
{
    $this->Etat = $Etat;

    return $this;
}

}
