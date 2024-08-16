<?php

declare(strict_types=1);

namespace App\CarMaster\Entity;

use App\Repository\SparePartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\{Column, Entity, GeneratedValue, Id, ManyToMany, OneToMany, Table};
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Attribute as Serialize;

#[Entity(repositoryClass: SparePartRepository::class)]
#[Table(name: 'spare_part')]
class SparePart
{
    #[Id]
    #[GeneratedValue]
    #[Column(name: 'spare_part_id', type: Types::INTEGER)]
    #[Serialize\Groups(['part_list', 'part_item'])]

    protected int $partId;

    #[Column(name: 'name_part', length: 60)]
    #[Assert\Length(min: 5, minMessage: 'Name part must be at least {{ limit }} characters long'
    )]
    #[Serialize\Groups(['part_item'])]
    protected string $namePart;

    #[Column(name: 'model_part', length: 60)]
    #[Assert\Length(min: 4, minMessage: 'Model part must be at least {{ limit }} characters long')]
    #[Serialize\Groups(['part_item'])]
    protected string $modelPart;

    #[Column(name: 'price_part', type: Types::FLOAT)]
    #[Assert\GreaterThan(value: 0)]
    protected float $pricePart;

    #[ManyToMany(targetEntity: Vehicle::class, mappedBy: 'spareParts', cascade: ["persist"])]
    private Collection $vehicles;

    #[OneToMany(targetEntity: OrderItem::class, mappedBy: 'spare_part', cascade: ["persist"])]
    protected Collection $orderItems;

    public function __construct()
    {
        $this->vehicles = new ArrayCollection();
        $this->orderItems = new ArrayCollection();
    }

    /*
     * отримуємо інфо про запчастину
     */
    public function getPartInfo(): array
    {
        return [
            'partId' => $this->getPartId(),
            'namePart' => $this->getNamePart(),
            'modelPart' => $this->getModelPart(),
            'pricePart' => $this->getPricePart()
        ];
    }

    /*
     * зв'язок з машиною
     */
    public function addVehicle(Vehicle $vehicle): self
    {
        if (!$this->vehicles->contains($vehicle)) {
            $this->vehicles[] = $vehicle;
            $vehicle->addSpareParts($this);
        }
        return  $this;
    }

    public function getVehicles(): Collection
    {
        return $this->vehicles;
    }

    public function removeVehicle(Vehicle $vehicle): self
    {$this->vehicles->removeElement($vehicle);
        $vehicle->removeSpareParts($this);

        return $this;
    }
        /*
 * взаємозв'язок з таблицею OrderItem
 */
    public function addOrderItem(OrderItem $orderItem): void
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems[] = $orderItem;
            $orderItem->addSpareParts($this);
        }
    }

    public function getOrderItem(): Collection
    {
        return $this->orderItems;
    }

    public function getModelPart(): string
    {
        return $this->modelPart;
    }

    public function setModelPart(string $modelPart): void
    {
        $this->modelPart = $modelPart;
    }

    public function getNamePart(): string
    {
        return $this->namePart;
    }

    public function setNamePart(string $namePart): void
    {
        $this->namePart = $namePart;
    }

    public function getPricePart(): float
    {
        return $this->pricePart;
    }

    /**
     * @param float $pricePart
     */
    public function setPricePart(float $pricePart): void
    {
        $this->pricePart = $pricePart;
    }

    /**
     * @return int
     */
    public function getPartId(): int
    {
        return $this->partId;
    }

        /**
     * @return Collection
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }
}