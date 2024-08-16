<?php

declare(strict_types=1);

namespace App\CarMaster\DTO;

use App\CarMaster\Entity\Enum\BodyTypes;
use Symfony\Component\Validator\Constraints as Assert;

class CreateCar
{
    public function __construct(
        #[Assert\Length(min: 6, minMessage: "Brand must be at least {{ limit }} characters long")]
public string $licensePlate,
        #[Assert\Positive]
        public int $ownerId,
        #[Assert\Positive]
        public array $partId,
        #[Assert\Length(min: 4, minMessage: "Brand must be at least {{ limit }} characters long")]
        public string $brand,
        #[Assert\Positive]
        public int $yearManufacture,
        #[Assert\NotBlank]
        #[Assert\Type('array')]
        public array $bodyTypes
    ) {
    }

}