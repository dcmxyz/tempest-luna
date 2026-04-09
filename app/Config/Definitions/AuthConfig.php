<?php

declare(strict_types=1);

namespace App\Config\Definitions;

final readonly class AuthConfig
{
    public function __construct(
        public bool $loginAfterRegister,
    ) {}
}
