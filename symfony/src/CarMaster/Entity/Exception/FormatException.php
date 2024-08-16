<?php

declare(strict_types=1);

namespace App\CarMaster\Entity\Exception;

class FormatException extends ValidationException
{
    public function __construct()
    {
        parent::__construct('Invalid data input format');
    }
}