<?php

declare(strict_types=1);

namespace App\CarMaster\Entity;

use App\CarMaster\Service\CalculableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Symfony\Component\Serializer\Attribute as Serialize;


#[Entity]
#[Table(name: 'service_order')]
class ServiceOrder implements CalculableInterface
{
    #[Id]
    #[GeneratedValue]
    #[Column(name: 'id_order', type: Types::INTEGER)]
    protected int $idOrder;

    #[Column(name: 'order_number', type: Types::INTEGER)]
    #[Serialize\Groups(['order_item'])]
    private int $orderNumber;

    #[ManyToOne(targetEntity: Vehicle::class, inversedBy: 'orders')]
    #[JoinColumn(name: 'vehicle_id', referencedColumnName: 'vehicle_id')]
    private Vehicle $vehicle;

    #[OneToMany(targetEntity: OrderItem::class, mappedBy: 'serviceOrder', cascade: ["persist"])]
    protected Collection $orderItems;

    public function __construct(Vehicle $vehicle, int $orderNumber)
    {
        $this->vehicle = $vehicle;
        $this->setOrderNumber($orderNumber);
        $this->orderItems = new ArrayCollection();
    }

    /*
     * Возвращает транспортное средство, связанное с этим заказом
     */
    public function getVehicle(): Vehicle
    {
        return $this->vehicle;
    }

    /*
     *устанавливает транспортное средство, связанное с этим заказом
     */
    public function setVehicle(Vehicle $vehicle): void
    {
        $this->vehicle = $vehicle;
    }

    /*
     * установка взаимной связи с OrderItem
     */

    public function addOrderItem(OrderItem $orderItem): void
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems[] = $orderItem;
            $orderItem->addServiceOrder($this);
        }
    }

    /**
     * @return Collection Коллекция объектов OrderItem для текущего ордера.
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    /**
     * @return int
     */
    public function getOrderNumber(): int
    {
        return $this->orderNumber;
    }

    /*
        * суммируем количество однотипных деталей - перебираем массив, для каждого $orderItem в массиве ордеров
        * проверяем есть ли деталь по ее айди в orderItem и в зависимости от того, есть или нет либо суммируем заданное
        * количество к уже существующему либо создаем новый orderItem и записываем туда переданное количество
        */
    public function updateOrderQuantities(SparePart $sparePart, int $quantity): int
    {
        foreach ($this->getOrderItems() as $orderItem) {
            if ($orderItem->getSparePart()->getPartId() === $sparePart->getPartId()) {
                if ($quantity !== 0) {
                    $newQuantity = $orderItem->getQuantity() + $quantity;
                    $orderItem->setQuantity($newQuantity);
                    return $newQuantity;
                } else {
                    $this->orderItems->removeElement($orderItem);
                    return 0;
                }
            }
        }
        $orderItem = new OrderItem($this, $sparePart, $quantity);
        $this->addOrderItem($orderItem);
        return $quantity;
    }

    /**
     * @param int $orderNumber
     */
    public function setOrderNumber(int $orderNumber): void
    {
        $this->orderNumber = $orderNumber;
    }

    /*
     *Реалізація методу інтерфейсу для отримання елементів через Collection $orderItems
     * та перетворення в масив вбудованою функцією колекції.
     */
    public function getItems(): array
    {
        return $this->orderItems->toArray();
    }
}