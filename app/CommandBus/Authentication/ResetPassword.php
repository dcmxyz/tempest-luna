<?php

declare(strict_types=1);

namespace App\CommandBus\Authentication;

final class ResetPassword
{
    public function __construct(
        public readonly string $token,
        public readonly string $password,
    ) {}
}