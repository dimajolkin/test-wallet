<?php

namespace App\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\ValidatorException;

class ValidationException extends ValidatorException
{
    public function __construct(ConstraintViolationListInterface $violations)
    {
        $messages = [];
        foreach ($violations as $violation) {
            $messages[] = $violation->getMessage();
        }
        $this->message = implode(', ', $messages);
    }
}
