<?php
declare(strict_types=1);

namespace App\CarMaster\Entity\Enum;

use App\CarMaster\Entity\Car;

enum VehicleType: string
{
    public const ENTITY_MAP = [ self::CAR->name=>Car::class
        ];
case CAR = 'Car';
public function entity(): object
{
return new (self::ENTITY_MAP[$this->name])();
}
}
