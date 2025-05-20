<?php

namespace App\Entity;

use App\Repository\CarRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CarRepository::class)]
class Car
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 25)]
    private ?string $brand = null;

    #[ORM\Column(length: 10)]
    private ?string $model = null;

    #[ORM\Column]
    private ?int $year = null;

    #[ORM\Column(length: 25)]
    private ?string $carLicense = null;

    #[ORM\Column]
    private ?bool $available = null;

    /**
     * @var Collection<int, Rental>
     */
    #[ORM\OneToMany(targetEntity: Rental::class, mappedBy: 'car')]
    private Collection $rentals;

    /**
     * @var Collection<int, CarImage>
     */
    #[ORM\OneToMany(
        targetEntity: CarImage::class, 
        mappedBy: 'Car', 
        cascade: ['persist', 'remove'], 
        orphanRemoval: true
    )]
    private Collection $carImages;

    public function __construct()
    {
        $this->rentals = new ArrayCollection();
        $this->carImages = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getCarLicense(): ?string
    {
        return $this->carLicense;
    }

    public function setCarLicense(string $carLicense): static
    {
        $this->carLicense = $carLicense;

        return $this;
    }

    public function isAvailable(): ?bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): static
    {
        $this->available = $available;

        return $this;
    }

    /**
     * @return Collection<int, Rental>
     */
    public function getRentals(): Collection
    {
        return $this->rentals;
    }

    public function addRental(Rental $rental): static
    {
        if (!$this->rentals->contains($rental)) {
            $this->rentals->add($rental);
            $rental->setCar($this);
        }

        return $this;
    }

    public function removeRental(Rental $rental): static
    {
        if ($this->rentals->removeElement($rental)) {
            // set the owning side to null (unless already changed)
            if ($rental->getCar() === $this) {
                $rental->setCar(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CarImage>
     */
    public function getCarImages(): Collection
    {
        return $this->carImages;
    }

    public function addCarImage(CarImage $carImage): static
    {
        if (!$this->carImages->contains($carImage)) {
            $this->carImages->add($carImage);
            $carImage->setCar($this);
        }

        return $this;
    }

    public function removeCarImage(CarImage $carImage): static
    {
        if ($this->carImages->removeElement($carImage)) {
            // set the owning side to null (unless already changed)
            if ($carImage->getCar() === $this) {
                $carImage->setCar(null);
            }
        }

        return $this;
    }
}
