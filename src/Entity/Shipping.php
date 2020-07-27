<?php

namespace App\Entity;

use App\Repository\ShippingRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ShippingRepository::class)
 */
class Shipping
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $clientFirstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $clientLastName;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adressLine1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adressLine2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $zipcode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdressLine1(): ?string
    {
        return $this->adressLine1;
    }

    public function setAdressLine1(string $adressLine1): self
    {
        $this->adressLine1 = $adressLine1;

        return $this;
    }

    public function getAdressLine2(): ?string
    {
        return $this->adressLine2;
    }

    public function setAdressLine2(?string $adressLine2): self
    {
        $this->adressLine2 = $adressLine2;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getZipcode(): ?int
    {
        return $this->zipcode;
    }

    public function setZipcode(int $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getClientFirstName(): ?string
    {
        return $this->clientFirstName;
    }

    public function setClientFirstName(?string $clientFirstName): self
    {
        $this->clientFirstName = $clientFirstName;

        return $this;
    }

    public function getClientLastName(): ?string
    {
        return $this->clientLastName;
    }

    public function setClientLastName(?string $clientLastName): self
    {
        $this->clientLastName = $clientLastName;

        return $this;
    }
}
