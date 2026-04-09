<?php

declare(strict_types=1);

namespace App\CommandBus\Authentication;

final readonly class PasswordResetEmail
{
    public function __construct(
        public string $email,
    ) {}
}
