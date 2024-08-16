<?php

declare(strict_types=1);

namespace App\CarMaster\Entity\Exception;
use Exception;

class FileOperationException extends Exception
{
    public function __construct()
    {
        parent::__construct("Request for file recording time");
    }
}