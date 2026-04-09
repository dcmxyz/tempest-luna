<?php

declare(strict_types=1);

namespace App\CommandBus\Authentication;

final readonly class LoginUser
{
    public function __construct(
        public string $email,
        #[\SensitiveParameter]
        public string $password,
    ) {}
}
