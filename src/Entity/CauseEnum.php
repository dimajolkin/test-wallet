<?php

namespace App\Entity;

class CauseEnum
{
    public const STOCK = 'stock';
    public const REFUND = 'refund';

    public const ALL = [self::STOCK, self::REFUND];
}
