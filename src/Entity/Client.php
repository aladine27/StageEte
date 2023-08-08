<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Doctrine\Common\Collections\ArrayCollection;
#[ORM\Entity(repositoryClass: "App\Repository\ClientRepository")]
class Client implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255)]
    private $Nomprenom;

    #[ORM\Column(type: "string", length: 255)]
    private $Adresse;

    #[ORM\Column(type: "string", length: 255)]
    private $Mail;

    #[ORM\Column(type: "string", length: 255)]
    private $Motdepasse;

    #[ORM\Column(type: "string", length: 20)]
    private $Tel;

    #[ORM\Column(type: "string", length: 20)]
    private $Cin;

    // ...

    #[ORM\OneToMany(targetEntity: "App\Entity\Contrat", mappedBy: "client")]
    private $contrats;

    public function __construct()
    {
        $this->contrats = new ArrayCollection();
    }

    // ...

    /**
     * @return ArrayCollection|Contrat[]
     */
    public function getContrats()
    {
        return $this->contrats;
    }

    public function addContrat(Contrat $contrat): self
    {
        if (!$this->contrats->contains($contrat)) {
            $this->contrats[] = $contrat;
            $contrat->setClient($this);
        }

        return $this;
    }

    public function removeContrat(Contrat $contrat): self
    {
        if ($this->contrats->contains($contrat)) {
            $this->contrats->removeElement($contrat);
            // set the owning side to null (unless already changed)
            if ($contrat->getClient() === $this) {
                $contrat->setClient(null);
            }
        }

        return $this;
    }

     public function getUsername(): string
    {
        return $this->Mail;
    }

    public function getRoles(): array
    {
        // You can define the roles for the client here.
        // For example, you can return an array with 'ROLE_USER' as a role.
        return ['ROLE_USER'];
    }

    public function getPassword(): string
    {
        return $this->Motdepasse;
    }

    // Other methods required by UserInterface (not necessary for basic authentication)

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials()
    {
        // If you have any sensitive data in the entity, remove it here.
        // For example, if you have a plain password field that you don't need after authentication.
        $this->Motdepasse = null;
    }

    // Getters and Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomprenom(): ?string
    {
        return $this->Nomprenom;
    }

    public function setNomprenom(string $Nomprenom): self
    {
        $this->Nomprenom = $Nomprenom;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->Adresse;
    }

    public function setAdresse(string $Adresse): self
    {
        $this->Adresse = $Adresse;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->Mail;
    }

    public function setMail(string $Mail): self
    {
        $this->Mail = $Mail;

        return $this;
    }

    public function getMotdepasse(): ?string
    {
        return $this->Motdepasse;
    }

    public function setMotdepasse(string $Motdepasse): self
    {
        $this->Motdepasse = $Motdepasse;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->Tel;
    }

    public function setTel(string $Tel): self
    {
        $this->Tel = $Tel;

        return $this;
    }

    public function getCin(): ?string
    {
        return $this->Cin;
    }

    public function setCin(string $Cin): self
    {
        $this->Cin = $Cin;

        return $this;
    }

    // You can also add more methods or business logic to this entity if needed
}
