<?php

namespace App\CarMaster\Manager;

use App\CarMaster\DTO\CreateCar;
use App\CarMaster\DTO\UpdateCar;
use App\CarMaster\Entity\Car;
use App\CarMaster\Entity\CarOwner;
use App\CarMaster\Entity\Enum\BodyTypes;
use App\Repository\SparePartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use UnexpectedValueException;

readonly class CarManagerAPI
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SparePartRepository $sparePartRepository,
    ) {
    }

    public function createVehicle(CreateCar $createVehicle): Car
    {
        $spareParts = $this->sparePartRepository->findBy(['partId' => $createVehicle->partId]);
        if (!$spareParts) {
            throw new EntityNotFoundException('Not find spare part');
        }
        $owner = $this->entityManager->getRepository(CarOwner::class)->find($createVehicle->ownerId);
        if (!$owner) {
            throw new EntityNotFoundException('Not find owner');
        }
        $bodyTypes = [];
        foreach ($createVehicle->bodyTypes as $bodyType) {
            try {
                $bodyTypeEnum = BodyTypes::from($bodyType);
                $bodyTypes[] = $bodyTypeEnum;
            } catch (UnexpectedValueException $e) {
            }
        }

        $car = new Car();
        $car->setLicensePlate($createVehicle->licensePlate);
        $car->setYearManufacture($createVehicle->yearManufacture);
        $car->setBrand($createVehicle->brand);
        $car->setOwner($owner);
        $car->setBodyTypes($bodyTypes);


        foreach ($spareParts as $sparePart) {
            $car->addSpareParts($sparePart);
        }
        $this->entityManager->persist($car);
        $this->entityManager->flush();
        return $car;
    }

    public function updateCar(UpdateCar $updateCar, Car $car): Car
    {
        if ($updateCar->licensePlate !== null) {
            $car->setLicensePlate($updateCar->licensePlate);
        }
        if ($updateCar->brand !== null) {
            $car->setBrand($updateCar->brand);
        }
        if ($updateCar->yearManufacture !== null) {
            $car->setYearManufacture($updateCar->yearManufacture);
        }
        $this->entityManager->flush();

        return $car;
    }

}



