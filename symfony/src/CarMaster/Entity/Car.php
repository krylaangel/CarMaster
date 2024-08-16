<?php

declare(strict_types=1);

namespace App\CarMaster\Entity;

use App\CarMaster\Entity\Enum\BodyTypes;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Serializer\Attribute\Groups;

#[Entity]
class Car extends Vehicle

{
    /** @var BodyTypes[] */
    #[Column(type: 'simple_array', nullable: true, enumType: BodyTypes::class)]
    #[Groups(['bodyTypes'])]
    private array $bodyTypes = [BodyTypes::NONE];

    public function __construct() {
        parent::__construct();
    }

    public function getInformation(): array
    {
        $data = parent::getInformation();
        $data['Body Type'] = $this->getBodyTypes();
        return $data;
    }

    /**
     * @return array
     */
    public function getBodyTypes(): array
    {
        return $this->bodyTypes;
    }

    /**
     * @param array $bodyTypes
     */
    public function setBodyTypes(array $bodyTypes): void
    {
        array_walk($bodyTypes, fn($bodyType) => $bodyType instanceof BodyTypes);
        $this->bodyTypes = $bodyTypes;
    }


}