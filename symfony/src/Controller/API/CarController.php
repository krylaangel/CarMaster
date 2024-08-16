<?php

namespace App\Controller\API;

use App\CarMaster\DTO\CreateCar;
use App\CarMaster\DTO\UpdateCar;
use App\CarMaster\Entity\Car;
use App\CarMaster\Manager\CarManagerAPI;
use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/cars')]
class CarController extends AbstractController
{
    #[Route('/', methods: ['GET'], format: 'json')]
    public function list(
        VehicleRepository $vehicleRepository,
        SerializerInterface $serializer,
        #[MapQueryParameter] int $page = 1
    ): Response {
        return new Response(
            $serializer->serialize(
                $vehicleRepository->findPage($page),
                'json',
                ['groups' => ['vehicle_list', 'owner_list', 'part_list']]
            )
        );
    }

    #[Route('/{vehicleId}', methods: ['GET'], format: 'json')]
    public function get(
        Car $car,
        SerializerInterface $serializer
    ): Response {
        return new Response(
            $serializer->serialize(
                $car, 'json',
                [
                    'groups' => [
                        'bodyTypes',
                        'vehicle_item',
                        'owner_item',
                        'owner_list',
                        'order_item',
                        'part_list',
                        'part_item'
                    ],
                ]
            )
        );
    }

    #[Route('/', methods: ['POST'], format: 'json')]
    public function create(
        #[MapRequestPayload] CreateCar $createVehicle,
        CarManagerAPI $carManager,
        SerializerInterface $serializer
    ): Response {
        return new Response(
            $serializer->serialize(
                $carManager->createVehicle($createVehicle),
                'json',
                ['groups' => ['vehicle_item', 'owner_item', 'part_item']]
            ),
            Response::HTTP_CREATED
        );
    }

    #[Route('/{vehicleId}', methods: ['PATCH'], format: 'json')]
    public function update(
        Car $car,
        #[MapRequestPayload] UpdateCar $updateCar,
        CarManagerAPI $carManager,
        SerializerInterface $serializer
    ): Response {
        return new Response(
            $serializer->serialize(
                $carManager->updateCar($updateCar, $car),
                'json',
                ['groups' => ['vehicle_update']]
            )
        );
    }

    #[Route('/{vehicleId}', methods: ['DELETE'], format: 'json')]
    public function delete(Car $car, EntityManagerInterface $entityManager, LoggerInterface $logger): Response
    {
        try {
            $entityManager->remove($car);
            $entityManager->flush();
        } catch (Exception $e) {
            $logger->error($e->getMessage());
            return new Response('Error an deleting', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}