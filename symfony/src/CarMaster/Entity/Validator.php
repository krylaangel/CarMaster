<?php

declare(strict_types=1);

namespace App\CarMaster\Entity;

use App\CarMaster\Entity\Exception\FormatException;
use App\CarMaster\Entity\Exception\InputException;
use App\CarMaster\Entity\Exception\LengthException;

class Validator
{
    public function validateNamePart($namePart)
    {
        if (!empty($namePart) && preg_match('/^[a-zA-Z.\s]+$/', $namePart)) {
            return $namePart;
        } else {
            throw new FormatException;
        }
    }

    public function validateCharacterCount($countCharacter, int $minCountCharacter): bool
    {
        $numberLength = strlen((string)$countCharacter);
        if ($numberLength >= $minCountCharacter) {
            return true;
        } else {
            throw new LengthException($minCountCharacter);
        }
    }

    /*
     * перевірка на існування ввода даних (порожні данні)
     */

    public function verifyInputFields($inputData): void
    {
        if (!empty($inputData)) {
        } else {
            throw new InputException();
        }
    }
/*
 * перевірка на числові значення полів, які не можуть буть меньшими за ноль. Наприклад, ціна та ін.
 */
    public function checkMinimumValue($value): bool
    {
        if ($value > 0) {
            return true;
        } else {
            throw new InputException('Value must be greater than 0');
        }
    }
}

