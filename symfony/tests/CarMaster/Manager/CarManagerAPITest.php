<?php

declare(strict_types=1);


namespace App\Tests\CarMaster\Manager;

use App\CarMaster\DTO\CreateCar;
use App\CarMaster\DTO\UpdateCar;
use App\CarMaster\Entity\Car;
use App\CarMaster\Entity\CarOwner;
use App\CarMaster\Entity\Enum\BodyTypes;
use App\CarMaster\Entity\SparePart;
use App\CarMaster\Manager\CarManagerAPI;
use App\Repository\SparePartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

class CarManagerAPITest extends TestCase
{
    private function createCarDTO(): CreateCar
    {
        return new CreateCar(
            'k6k6k6k',
            5,
            [5, 6],
            'Tesla',
            2024,
            [BodyTypes::WAGON->value]
        );
    }

    private function createMocks(): array
    {
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $sparePartRepositoryMock = $this->createMock(SparePartRepository::class);
        $carOwnerRepositoryMock = $this->createMock(EntityRepository::class);

        return [$entityManagerMock, $sparePartRepositoryMock, $carOwnerRepositoryMock];
    }

    private function mockOwnerRepository($entityManagerMock, $carOwnerRepositoryMock): void
    {
        $entityManagerMock->expects($this->once())
            ->method('getRepository')
            ->with(CarOwner::class)
            ->willReturn($carOwnerRepositoryMock);
    }

    private function mockOwner($carOwnerRepositoryMock, $ownerIdValue): void
    {
        $carOwnerRepositoryMock->expects($this->once())
            ->method('find')
            ->with(5)
            ->willReturn($ownerIdValue);
    }

    private function mockParts($sparePartRepositoryMock, $spareParts): void
    {
        $sparePartRepositoryMock->expects($this->once())
            ->method('findBy')
            ->with(['partId' => [5, 6]])
            ->willReturn($spareParts);
    }

    public function testCreateVehicle()
    {
        [$entityManagerMock, $sparePartRepositoryMock, $carOwnerRepositoryMock] = $this->createMocks();
        $this->mockOwnerRepository($entityManagerMock, $carOwnerRepositoryMock);

        $carOwnerMock = $this->createMock(CarOwner::class);
        $carOwnerMock->method('getOwnerId')->willReturn(5);
        $this->mockOwner($carOwnerRepositoryMock, $carOwnerMock);

        $sparePartMockFirst = $this->createMock(SparePart::class);
        $sparePartMockSecond = $this->createMock(SparePart::class);

        $this->mockParts($sparePartRepositoryMock, [$sparePartMockFirst, $sparePartMockSecond]);

        $carDTO = $this->createCarDTO();

        $entityManagerMock->expects($this->once())
            ->method('persist')
            ->with(
                $this->callback(function ($car) use ($carDTO) {
                    $this->assertInstanceOf(Car::class, $car);
                    return true;
                })
            );

        $entityManagerMock->expects($this->once())->method('flush');

        $carManagerApi = new CarManagerAPI($entityManagerMock, $sparePartRepositoryMock);
        $car = $carManagerApi->createVehicle($carDTO);
        $this->assertInstanceOf(Car::class, $car);
        $this->assertEquals('k6k6k6k', $car->getLicensePlate());
        $this->assertEquals(5, $car->getOwner()->getOwnerId());
        $this->assertEquals('Tesla', $car->getBrand());
        $this->assertEquals(2024, $car->getYearManufacture());
        $this->assertEquals([BodyTypes::WAGON], $car->getBodyTypes());
        $spareParts = $car->getSpareParts();

        $this->assertContains($sparePartMockFirst, $spareParts);
        $this->assertContains($sparePartMockSecond, $spareParts);
    }

    public function testOwnerException()
    {
        [$entityManagerMock, $sparePartRepositoryMock, $carOwnerRepositoryMock] = $this->createMocks();

        $this->mockOwnerRepository($entityManagerMock, $carOwnerRepositoryMock);
        $this->mockOwner($carOwnerRepositoryMock, null);

        $sparePartMockFirst = $this->createMock(SparePart::class);
        $sparePartMockSecond = $this->createMock(SparePart::class);

        $this->mockParts($sparePartRepositoryMock, [$sparePartMockFirst, $sparePartMockSecond]);
        $carDTO = $this->createCarDTO();

        $carManagerApi = new CarManagerAPI($entityManagerMock, $sparePartRepositoryMock);
        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Not find owner');
        $carManagerApi->createVehicle($carDTO);
    }

    public function testPartsException()
    {
        [$entityManagerMock, $sparePartRepositoryMock] = $this->createMocks();
        $entityManagerMock->expects($this->never())
            ->method('getRepository');

        $this->mockParts($sparePartRepositoryMock, []);

        $carDTO = $this->createCarDTO();

        $carManagerApi = new CarManagerAPI($entityManagerMock, $sparePartRepositoryMock);
        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Not find spare part');
        $carManagerApi->createVehicle($carDTO);
    }

    public function updateCarDataProvider(): array
    {
        return [
            'update all fields' => [
                new UpdateCar('new License Plate', 'new brand', 2024),
                'setLicensePlate' => true,
                'setBrand' => true,
                'setYearManufacture' => true
            ],
            'update no fields' => [
                new UpdateCar(null, null, null),
                'setLicensePlate' => false,
                'setBrand' => false,
                'setYearManufacture' => false
            ],
            'update only LicensePlate' => [
                new UpdateCar('new License Plate', null, null),
                'setLicensePlate' => true,
                'setBrand' => false,
                'setYearManufacture' => false
            ],
            'update only brand' => [
                new UpdateCar(null, 'new brand', null),
                'setLicensePlate' => false,
                'setBrand' => true,
                'setYearManufacture' => false
            ],
            'update only Year Manufacture' => [
                new UpdateCar(null, null, 2024),
                'setLicensePlate' => false,
                'setBrand' => false,
                'setYearManufacture' => true
            ],
        ];
    }

    /**
     * @dataProvider updateCarDataProvider
     */
    public function testUpdateCar(UpdateCar $updateCarDTO, bool $expectSetLicensePlate, bool $expectSetBrand, bool $expectSetYearManufacture)
    {
        [$entityManagerMock, $sparePartRepositoryMock] = $this->createMocks();
        $carMock = $this->createMock(Car::class);

        if ($expectSetLicensePlate) {
            $carMock->expects($this->once())
                ->method('setLicensePlate')
                ->with($updateCarDTO->licensePlate);
        } else {
            $carMock->expects($this->never())->method('setLicensePlate');
        }

        if ($expectSetBrand) {
            $carMock->expects($this->once())
                ->method('setBrand')
                ->with($updateCarDTO->brand);
        } else {
            $carMock->expects($this->never())->method('setBrand');
        }

        if ($expectSetYearManufacture) {
            $carMock->expects($this->once())
                ->method('setYearManufacture')
                ->with($updateCarDTO->yearManufacture);
        } else {
            $carMock->expects($this->never())->method('setYearManufacture');
        }

        $entityManagerMock->expects($this->once())->method('flush');

        $carManagerApi = new CarManagerAPI($entityManagerMock, $sparePartRepositoryMock);
        $car = $carManagerApi->updateCar($updateCarDTO, $carMock);
        $this->assertInstanceOf(Car::class, $car);
    }
}