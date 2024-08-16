<?php

namespace App\CarMaster\Service;

class ServiceCostCalculator implements CostCalculatorInterface
{
    public function calculateTotalCost(CalculableInterface $calculable): float
    {
        /*
         * Вбудована функція, яка приймає параметром потрібний масив (проходить по кожному елементу)
         * і анонімну функцію, що визначає накопичене значення суми елементів масиву ($carry)
         * і поточний елемент масиву $item, + початкове значення $carry = 0
         */
        return array_reduce($calculable->getItems(), function ($carry, $item) {
            return $carry + $item->getSparePart()->getPricePart() * $item->getQuantity();
        }, 0);
    }

}