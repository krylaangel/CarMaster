<?php

declare(strict_types=1);

namespace App\CarMaster\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\{Column, Entity, GeneratedValue, Id, OneToMany, Table};
use Symfony\Component\Serializer\Attribute as Serialize;


#[Entity]
#[Table(name: 'car_owner')]
class CarOwner
{
    protected Validator $validator;

    #[Id]
    #[GeneratedValue]
    #[Column(name: 'owner_id', type: Types::INTEGER)]
    #[Serialize\Groups(['owner_item'])]
    protected int $ownerId;

    #[Column(name: 'first_name', length: 20)]
    #[Serialize\Groups(['owner_list', 'owner_item'])]
    private string $firstName;

    #[Column(name: 'last_name', length: 20)]
    #[Serialize\Groups(['owner_list', 'owner_item'])]
    private string $lastName;

    #[Column(name: 'password', length: 60)]
    private string $password;

    #[Column(name: 'email', length: 30)]
    #[Serialize\Groups(['owner_item'])]
    private string $ownerEmail;

    #[Column(name: 'phone_number', type: Types::BIGINT)]
    #[Serialize\Groups(['owner_item'])]
    private int $contactNumber;

    #[OneToMany(targetEntity: Vehicle::class, mappedBy: 'owner', cascade: ["persist"])]
    protected Collection $vehicles;


    public function __construct(
        string $firstName,
        string $lastName,
        string $password,
        int $contactNumber,
        string $ownerEmail,
        Validator $validator
    ) {
        $this->validator = $validator;
        $this->vehicles = new ArrayCollection();
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
public function getFullName(): string
{
    return $this->firstName . ' ' . $this-> lastName;
}
    public function setVehicles(Vehicle $vehicles): void
    {
        $this->vehicles[] = $vehicles;
        $vehicles->setOwner($this);

    }
    public function getVehicles(): Collection
    {
        return $this->vehicles;
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

    /**
     * @return int
     */
    public function getOwnerId(): int
    {
        return $this->ownerId;
    }
}