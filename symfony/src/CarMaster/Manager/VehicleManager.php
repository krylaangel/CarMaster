<?php

declare(strict_types=1);

namespace App\CarMaster\Manager;

use App\CarMaster\Entity\Car;
use App\CarMaster\Entity\CarOwner;
use App\CarMaster\Entity\Enum\BodyTypes;
use App\CarMaster\Entity\SparePart;
use App\CarMaster\Entity\Vehicle;
use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Generator;

readonly class VehicleManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Generator $faker,
        private VehicleRepository $vehicleRepository
    ) {
    }

    /*
        * создаем новое транспортное средство по существующему в базе владельцу.
        */
    public function createVehicleByOwner(CarOwner $owner): Vehicle
    {
        $randomBodyType = $this->faker->randomElement(BodyTypes::toArray());
        $car = new Car();
        $car->setOwner($owner);
        $car->setBrand($this->faker->company());
        $car->setYearManufacture($this->faker->numberBetween(1980, date('Y')));
        $car->setLicensePlate($this->faker->regexify('^([A-Z]{2}\d{4}[A-Z]{2})$'));
        $car->setBodyTypes([$randomBodyType]);
        $this->entityManager->persist($car);
        $this->entityManager->flush();
        return $car;
    }

    public function getVehiclesInfoByOwner(int $contactNumber): array
    {
        $vehicles = $this->vehicleRepository->findVehiclesByOwner($contactNumber);
        $result = [];
        foreach ($vehicles as $vehicle) {
            $vehicleInfo = $vehicle->getInformation();
            $result [] = $vehicleInfo;
        }
        return $result;
    }

    public function removeVehicleByDB(Vehicle $vehicle): void
    {
        $this->entityManager->remove($vehicle);
        $this->entityManager->flush();
    }
}