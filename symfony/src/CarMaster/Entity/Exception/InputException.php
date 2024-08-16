<?php

declare(strict_types=1);

namespace App\CarMaster\Entity\Exception;

class InputException extends ValidationException
{
    public function __construct(string $message='Data entry validation error. Please check that the entered values are correct')
    {
        parent::__construct($message);
    }
}