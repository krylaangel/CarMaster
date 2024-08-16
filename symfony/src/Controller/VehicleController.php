<?php

namespace App\Controller;

use App\CarMaster\Entity\Enum\VehicleType;
use App\CarMaster\Entity\Vehicle;
use App\CarMaster\Manager\CacheManager;
use App\CarMaster\Manager\SparePartManager;
use App\CarMaster\Manager\VehicleManager;
use App\Form\FindPartType;
use App\Form\VehicleForm;
use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/vehicles')]
class VehicleController extends AbstractController
{
    #[Route('/', name: 'app_all_vehicles', methods: 'GET')]
    public function index(
        VehicleRepository $vehicleRepository,
        #[MapQueryParameter(
            filter: FILTER_VALIDATE_REGEXP,
            options: ['regexp' => '/^[1-9][0-9]*$/']
        )] $page = 1
    ): Response {
        return $this->render(
            'vehicle/index.html.twig',
            ['vehicles' => $vehicleRepository->findPage(max(1, $page))]
        );
    }

    #[Route('/{vehicleId}', name: 'app_vehicle_show', requirements: ['vehicleId' => '\d+'], methods: ['GET'])]
    public function show(Vehicle $vehicle): Response
    {
        return $this->render('vehicle/show_vehicle.html.twig', ['vehicle' => $vehicle]);
    }

    #[Route('/_create/{vehicleType}', name: 'app_create_vehicle', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, VehicleType $vehicleType): Response
    {
        $form = $this->createForm(VehicleForm::class, $vehicleType->entity());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($vehicle = $form->getData());
            $entityManager->flush();
            return $this->redirectToRoute('app_vehicle_show', ['vehicleId' => $vehicle->getVehicleId()]);
        }
        return $this->render('vehicle/create_vehicle.html.twig', ['form' => $form]);
    }


    #[Route('/{vehicleId}/_delete', name: 'app_vehicle_delete', requirements: ['vehicleId' => '\d+'])]
    public function delete(Vehicle $vehicle, VehicleManager $vehicleManager): Response
    {
        $vehicleManager->removeVehicleByDB($vehicle);
        $this->addFlash('success', "Vehicle deleted");
        return $this->redirectToRoute('app_all_vehicles');
    }

    /**
     * Пошук запчастини під конкретну машину за номером її (машини) ліцензії
     */
    #[Route('/_find', name: 'app_find_part_for_vehicles', methods: [
        'GET',
        'POST'
    ])]
    public function find(
        Request $request,
        SparePartManager $sparePartManager,
        EntityManagerInterface $entityManager,
        CacheManager $cache
    ): Response {
        $form = $this->createForm(FindPartType::class, options: ['method' => 'GET']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $search = $data['licensePlate'];
            $vehicle = $entityManager->getRepository(Vehicle::class)->findOneBy(['licensePlate' => $search]);
            if (!$vehicle) {
                $this->addFlash('error', "license plate not found");
            } else {
                $value = $vehicle->getLicensePlate();
                $spareParts = $sparePartManager->getFindPartsForCar($value);
                $cache->saveCache($spareParts, $value);

                if (!$spareParts) {
                    $this->addFlash('error', "Parts not found");
                    return $this->redirectToRoute('app_find_part_for_vehicles');
                } else {
                    return $this->render('vehicle/result_find.html.twig', [
                        'parts' => $spareParts,
                        'vehicle' => $vehicle
                    ]);
                }
            }
        }
        return $this->render('vehicle/find.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
