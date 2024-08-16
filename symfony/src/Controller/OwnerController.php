<?php

namespace App\Controller;

use App\CarMaster\Entity\CarOwner;
use App\CarMaster\Manager\OwnerManager;
use App\CarMaster\Manager\VehicleManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/owner')]

class OwnerController extends AbstractController
{
    /*
    * пошук власника за його Id
    */
    #[Route('/owner/{ownerId}', name: 'app_owner', methods: ['GET'])]
    public function findOwner(int $ownerId, EntityManagerInterface $entityManager): Response
    {
        $owner = $entityManager->getRepository(CarOwner::class)->find($ownerId);
        if (!$owner) {
            return new JsonResponse([
                'error' => 'Owner not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($owner->getOwnerInfo());
    }

    /*
    * створення нового власника
    */

    #[Route('/owner/create/{ownerId}', name: 'app_owner_create', methods: ['GET'])]
    public function create(int $ownerId, OwnerManager $ownerManager, EntityManagerInterface $entityManager): Response
    {
        $owner = $entityManager->getRepository(CarOwner::class)->find($ownerId);
        if ($owner) {
            return new JsonResponse([
                'error' => 'Owner already exists',
                'owner' => $owner->getOwnerInfo()
            ], Response::HTTP_CONFLICT);
        }
        return new JsonResponse($ownerManager->createOwner()->getOwnerInfo(), Response::HTTP_CREATED);
    }

        /**
     * Пошук машини за номером телефона її власника
     */
    #[Route('/_find', name: 'app_find_vehicle_for_owner', methods: [
        'GET',
        'POST'
    ])]
    public function find(
        VehicleManager $vehicleManager
    ): Response {
        $findCarByOwner = $vehicleManager->getVehiclesInfoByOwner($contactNumber);
        if (!empty($findCarByOwner)) {
            return new JsonResponse($findCarByOwner);
        }
        return new JsonResponse([
            'error' => 'Owner has no vehicle',
        ], Response::HTTP_NOT_FOUND);
    }

}
