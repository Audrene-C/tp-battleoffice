<?php

namespace App\Entity;

use App\Repository\AddressesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AddressesRepository::class)
 */
class Addresses
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Billing::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $Billing;

    /**
     * @ORM\OneToOne(targetEntity=Shipping::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $Shipping;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBilling(): ?Billing
    {
        return $this->Billing;
    }

    public function setBilling(Billing $Billing): self
    {
        $this->Billing = $Billing;

        return $this;
    }

    public function getShipping(): ?Shipping
    {
        return $this->Shipping;
    }

    public function setShipping(Shipping $Shipping): self
    {
        $this->Shipping = $Shipping;

        return $this;
    }
}
