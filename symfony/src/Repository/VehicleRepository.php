<?php

declare(strict_types=1);


namespace App\Repository;

use App\CarMaster\Entity\Vehicle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class VehicleRepository extends ServiceEntityRepository
{
    private const VEHICLE_PER_PAGE = 10;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicle::class);
    }

    /*
        * пошук машини за номером власника
        */
    public function findVehiclesByOwner(int $contactNumber): array
    {
        $queryBuilder = $this->createQueryBuilder('v')
            ->join('v.owner', 'o')
            ->where('o.contactNumber =:phone_number');
        $query = $queryBuilder->getQuery()
            ->setParameter('phone_number', $contactNumber);
        return $query->getResult();
    }

    /*
     * пошук машин за запчастиною
     */

    public function getVehicleInPart(int $partId): array
    {
        $queryBuilder = $this->createQueryBuilder('v')
            ->join('v.spareParts', 's')
            ->where('s.partId = :spare_part_id');
        $query = $queryBuilder->getQuery()
            ->setParameter('spare_part_id', $partId);
        return $query->getResult();
    }

    public function findPage(int $page = 1): array
    {
        return $this->createQueryBuilder('v')
            ->orderBy('v.brand', 'ASC')
            ->getQuery()
//            ->enableResultCache(60)
            ->setFirstResult(($page - 1) * self::VEHICLE_PER_PAGE)
            ->setMaxResults(self::VEHICLE_PER_PAGE)
            ->getResult();
    }
}

