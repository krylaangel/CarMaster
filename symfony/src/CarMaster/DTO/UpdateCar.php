<?php

namespace App\CarMaster\DTO;

use Symfony\Component\Validator\Constraints as Assert;


class UpdateCar
{
    public function __construct(
        #[Assert\Positive]
        #[Assert\Length(min: 6, minMessage: "Brand must be at least {{ limit }} characters long")]
        public ?string $licensePlate = null,

        #[Assert\Positive]
        #[Assert\Length(min: 4, minMessage: "Brand must be at least {{ limit }} characters long")]
        public ?string $brand = null,

        #[Assert\Positive]
        public ?int $yearManufacture = null,

    ) {
    }
}