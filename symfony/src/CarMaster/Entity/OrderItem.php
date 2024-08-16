<?php

declare(strict_types=1);

namespace App\CarMaster\Entity;


use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\{Column, Entity, GeneratedValue, Id, JoinColumn, ManyToOne, Table};


#[Entity]
#[Table(name: 'order_item')]
class OrderItem

{
    #[Id]
    #[GeneratedValue]
    #[Column(name: 'id_item', type: Types::INTEGER)]
    private int $idItem;

    #[ManyToOne(targetEntity: ServiceOrder::class, inversedBy: 'orderItems')]
    #[JoinColumn(name: 'id_order', referencedColumnName: 'id_order', nullable: false)]
    private ServiceOrder $serviceOrder;

    #[ManyToOne(targetEntity: SparePart::class)]
    #[JoinColumn(name: 'spare_part_id', referencedColumnName: 'spare_part_id', nullable: false)]
    private SparePart $sparePart;

    #[Column(type: 'integer')]
    private int $quantity;

    public function __construct(ServiceOrder $serviceOrder, SparePart $sparePart, int $quantity)
    {
        $this->serviceOrder = $serviceOrder;
        $this->sparePart = $sparePart;
        $this->quantity = $quantity;
    }

    public function addSpareParts(?SparePart $sparePart): void
    {
        $this->sparePart = $sparePart;
    }

    public function getSparePart(): SparePart
    {
        return $this->sparePart;
    }

    public function getServiceOrder(): ServiceOrder
    {
        return $this->serviceOrder;
    }

    /*
     * установка взаимной связи с ServiceOrder
     */
    public function addServiceOrder(ServiceOrder $serviceOrder): void
    {
        $this->serviceOrder = $serviceOrder;
    }

    /**
     * @return int
     */
    public function getIdItem(): int
    {
        return $this->idItem;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }
}