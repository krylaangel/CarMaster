<?php

namespace App\CarMaster\Service;


interface CostCalculatorInterface
{
public function calculateTotalCost(CalculableInterface $calculable): float;
}