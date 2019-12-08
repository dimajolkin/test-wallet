<?php

namespace App\Service\CurrencyService\Operation;

use App\Entity\User;
use App\Service\CurrencyService\Money;
use App\Service\UserService\UserService;

class OperationService
{
    /**
     * @var UserService
     */
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function append(User $user, Money $money, string $cause): void
    {
    }
}
