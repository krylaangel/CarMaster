<?php

namespace App\Twig;

use App\CarMaster\Entity\Enum\VehicleType;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{

public function getFunctions(): array
{
return [new TwigFunction('vehicle_types', [$this, 'getVehicleTypes'])];
}
public function getVehicleTypes():array
{
return array_column(VehicleType::cases(), 'value');
}
}