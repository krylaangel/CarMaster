<?php

declare(strict_types=1);

namespace App\CarMaster\Manager;

use App\CarMaster\Entity\CarOwner;
use App\CarMaster\Entity\Validator;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Generator;

readonly class OwnerManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Generator $faker
    ) {
    }

    /*
     створюємо нового власника
    */

    public function createOwner(): CarOwner
    {
        $validator = new Validator();
        $owner = new CarOwner(
            $this->faker->firstName,
            $this->faker->lastName,
            $this->faker->password,
            (int)$this->faker->regexify('380[0-9]{9}'),
            $this->faker->email,
            $validator,
        );
        $this->entityManager->persist($owner);
        $this->entityManager->flush();
        return $owner;
    }

}