<?php

namespace App\Controller;

use App\CarMaster\Entity\ServiceOrder;
use App\CarMaster\Service\CostCalculatorInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class ServiceOrderController extends AbstractController
{
    /**
     * @throws Exception
     */
    #[Route('/service_order/calculate/{orderNumber}', name: 'app_calc_service_order', methods: ['GET'])]
    public function calculate(CostCalculatorInterface $costCalculator, ServiceOrder $serviceOrder): JsonResponse
    {
            return new JsonResponse([
                'Order number' => $serviceOrder->getOrderNumber(),
                'Brand vehicle' => $serviceOrder->getVehicle()->getBrand(),
                'License vehicle' => $serviceOrder->getVehicle()->getLicensePlate(),
                'Total cost' => $costCalculator->calculateTotalCost($serviceOrder)
            ]);

    }
}