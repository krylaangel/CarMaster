<?php

declare(strict_types=1);

namespace App\CarMaster\Entity;

use App\CarMaster\Entity\Enum\VehicleType;
use App\Repository\VehicleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\{Column,
    DiscriminatorColumn,
    DiscriminatorMap,
    Entity,
    GeneratedValue,
    Id,
    InheritanceType,
    InverseJoinColumn,
    JoinColumn,
    JoinTable,
    ManyToMany,
    ManyToOne,
    OneToMany,
    Table};
use Symfony\Component\Serializer\Attribute as Serialize;

#[Entity(repositoryClass: VehicleRepository::class)]
#[Table(name: 'vehicle')]
#[InheritanceType('SINGLE_TABLE')]
#[DiscriminatorColumn(name: 'type', type: 'string')]
#[DiscriminatorMap(VehicleType::ENTITY_MAP)]
abstract class Vehicle

{
    #[Id]
    #[GeneratedValue]
    #[Column(name: 'vehicle_id', type: Types::INTEGER)]
    #[Serialize\Groups(['vehicle_list', 'vehicle_item'])]
    protected int $vehicleId;

    #[Column(name: 'license_plate', length: 60)]
    #[Serialize\Groups(['vehicle_list', 'vehicle_item', 'vehicle_update'])]
    protected string $licensePlate;

    #[Column(name: 'year_manufacture', type: Types::INTEGER)]
    #[Serialize\Groups(['vehicle_list', 'vehicle_item', 'vehicle_update'])]
    protected int $yearManufacture;

    #[Column(name: 'brand', length: 60)]
    #[Serialize\Groups(['vehicle_list', 'vehicle_item', 'vehicle_update'])]
    protected string $brand;

    #[ManyToOne(targetEntity: CarOwner::class, inversedBy: 'vehicles')]
    #[JoinColumn(name: 'owner_id', referencedColumnName: 'owner_id')]
    #[Serialize\Groups(['vehicle_list', 'vehicle_item'])]
    private CarOwner $owner;
    #[ManyToMany(targetEntity: SparePart::class, inversedBy: 'vehicles', cascade: ["persist"])]
    #[JoinTable(name: 'car_spares_parts')]
    #[JoinColumn(name: 'vehicle_id', referencedColumnName: 'vehicle_id')]
    #[InverseJoinColumn(name: 'spare_part_id', referencedColumnName: 'spare_part_id')]
    #[Serialize\Groups(['vehicle_list', 'vehicle_item'])]
    private Collection $spareParts;

    #[OneToMany(targetEntity: ServiceOrder::class, mappedBy: 'vehicle', cascade: ["persist"])]
    #[Serialize\Groups('vehicle_item')]
    protected Collection $orders;

    public function __construct() {
        $this->spareParts = new ArrayCollection();
        $this->orders = new ArrayCollection();
    }

    public function getInformation(): array
    {
        return [
            'License Plate' => $this->getLicensePlate(),
            'Year of Manufacture' => $this->getYearManufacture(),
            'Brand' => $this->getBrand()
        ];
    }
    public function getType(): string
    {
return array_flip(VehicleType::ENTITY_MAP)[get_called_class()];
    }

    public function setOwner(CarOwner $owner): void
    {
        $this->owner = $owner;
    }

    /**
     * @return CarOwner
     */
    public function getOwner(): CarOwner
    {
        return $this->owner;
    }
    public function addSpareParts(SparePart $sparePart): void
    {
        if (!$this->spareParts->contains($sparePart)) {
            $this->spareParts[] = $sparePart;
            $sparePart->addVehicle($this);
        }
    }
    public function getSpareParts(): Collection
    {
        return $this->spareParts;
    }
    public function removeSpareParts(SparePart $sparePart): self
    {
        if ($this->spareParts->removeElement($sparePart)) {
            $sparePart->removeVehicle($this);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }
   public function getLicensePlate(): string
    {
        return $this->licensePlate;
    }

    public function setLicensePlate(string $licensePlate): void
    {
        $this->licensePlate = $licensePlate;
    }

    public function getYearManufacture(): int
    {
        return $this->yearManufacture;
    }

    public function setYearManufacture(int $yearManufacture): void
    {
        $this->yearManufacture = $yearManufacture;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): void
    {
        $this->brand = $brand;
    }

    public function getVehicleId(): int
    {
        return $this->vehicleId;
    }

}