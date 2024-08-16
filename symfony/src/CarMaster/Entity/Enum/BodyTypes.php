<?php
declare(strict_types=1);

namespace App\CarMaster\Entity\Enum;

use ReflectionClass;

enum BodyTypes: string
{
    case NONE = 'N/A';
    case SEDAN = 'Sedan';
    case SUV = 'SUV';
    case HATCHBACK = 'Hatchback';
    case COUPE = 'Coupe';
    case CONVERTIBLE = 'Convertible';
    case VAN = 'Van';
    case TRUCK = 'Truck';
    case WAGON = 'Wagon';

    public static function toArray(): array
    {
        return [
            self::SEDAN,
            self::SUV,
            self::HATCHBACK,
            self::COUPE,
            self::CONVERTIBLE,
            self::VAN,
            self::TRUCK,
            self::WAGON
        ];
    }

    public static function isValid(string $value): bool
    {
        $reflector = new ReflectionClass(self::class);
        if (in_array($value, $reflector->getConstants(), true)) {
            return true;
        }
        return false;
    }
}
