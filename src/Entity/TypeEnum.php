<?php

namespace App\Entity;

class TypeEnum
{
    const DEBIT = 'debit';
    const CREDIT = 'credit';
    const ALL = [self::DEBIT, self::CREDIT];
}
