<?php

declare(strict_types=1);

namespace App\CommandBus\Authentication;

final readonly class RegisterUser
{
    public function __construct(
        public string $name,
        public string $email,
        #[\SensitiveParameter]
        public string $password,
    ) {}
}
