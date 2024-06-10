<?php

declare(strict_types=1);

namespace App\CarMaster\Entity;

use App\CarMaster\Entity\Exception\FileOperationException;
use App\Repository\OwnerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\{Column, Entity, GeneratedValue, Id, OneToMany, Table};

use const CarMaster\Write_files\OWNER_CARS_INFO;

#[Entity ]
#[Table(name: 'car_owner')]
class CarOwner
{
    protected Validator $validator;

    #[Id]
    #[GeneratedValue]
    #[Column(name: 'owner_id', type: Types::INTEGER)]
    protected int $ownerId;

    #[Column(name: 'first_name', length: 20)]
    private string $firstName;
    #[Column(name: 'last_name', length: 20)]
    private string $lastName;
    #[Column(name: 'password', length: 60)]
    private string $password;
    #[Column(name: 'email', length: 30)]
    private string $ownerEmail;
    #[Column(name: 'phone_number', type: Types::BIGINT)]
    private int $contactNumber;

    #[OneToMany(targetEntity: Vehicle::class, mappedBy: 'owner', cascade: ["persist"] )]
    protected Collection $vehicleInfo;


    public function __construct(
        string $firstName,
        string $lastName,
        string $password,
        int $contactNumber,
        string $ownerEmail,
        Validator $validator
    ) {
        $this->validator = $validator;
        $this->vehicleInfo = new ArrayCollection();
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
        $this->setPassword($password);
        $this->setContactNumber($contactNumber);
        $this->setOwnerEmail($ownerEmail);
    }

    public function getOwnerInfo(): array
    {
        return [
            'First Name' => $this->getFirstName(),
            'Last Name' => $this->getLastName(),
            'Contact Number' => $this->getContactNumber(),
            'Email' => $this->getOwnerEmail(),
            'Password' => $this->getPassword()
        ];
    }

    public function writeOwnerInfoToFile(string $filenameCarOwner): void
    {
        $ownerInfo = $this->getOwnerInfo();
        $json_data = json_encode($ownerInfo, JSON_PRETTY_PRINT);
        file_put_contents($filenameCarOwner, $json_data);
    }

// находит только машины определенного владельца, создает массив

    public function findOwnerCars(): array
    {
        $findOwner = $this->getFirstName() . $this->getLastName();
        $findCar = [];
        foreach ($this->getVehicleInfo() as $vehicleInfo) {
            if ($vehicleInfo instanceof Car) {
                $findCar[] = $vehicleInfo->getInformation();
            }
        }
        return [
            'Owner' => $findOwner,
            'Cars' => $findCar
        ];
    }

    /**
     * записывает в файл инфо, полученную из метода findOwnerCars и возвращает ошибки
     * @throws FileOperationException
     */
    public function writeOwnerCarsInfo(): void
    {
        $jsonString = json_encode($this->findOwnerCars(), JSON_PRETTY_PRINT);
        if ($jsonString !== false) {
            $result = file_put_contents(OWNER_CARS_INFO, $jsonString);
            if ($result === false) {
                throw new FileOperationException("Ошибка при записи в файл: OWNER_CARS_INFO;");
            }
        } else {
            throw new FileOperationException("Ошибка кодирования JSON");
        }
    }

    public function addVehicle(Vehicle $vehicleInfo): void
    {
        $this->vehicleInfo[] = $vehicleInfo;
    }

    public function getVehicleInfo(): ArrayCollection|Collection
    {
        return $this->vehicleInfo;
    }

       /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
        $this->validator->validateCharacterCount($firstName, 2);
        $this->validator->validateNamePart($firstName);
        $this->firstName = $firstName;
        $this->validator->verifyInputFields($firstName);
    }

    public function getContactNumber(): int
    {
        return $this->contactNumber;
    }

    public function setContactNumber(int $contactNumber): void
    {
        $this->validator->validateCharacterCount($contactNumber, 12);
        $this->contactNumber = $contactNumber;
        $this->validator->verifyInputFields($contactNumber);
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
        $this->validator->verifyInputFields($lastName);
    }

    public function getOwnerEmail(): string
    {
        return $this->ownerEmail;
    }

    public function setOwnerEmail(string $ownerEmail): void
    {
        $this->ownerEmail = $ownerEmail;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
}