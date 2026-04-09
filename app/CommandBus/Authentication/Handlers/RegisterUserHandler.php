<?php

declare(strict_types=1);

namespace App\CommandBus\Authentication\Handlers;

use App\CommandBus\Authentication\RegisterUser;
use App\Models\User;
use Tempest\CommandBus\CommandHandler;
use Tempest\Cryptography\Password\PasswordHasher;
use Tempest\DateTime\DateTime;

final readonly class RegisterUserHandler
{
    public function __construct(
        private PasswordHasher $passwordHasher,
    ) {}

    #[CommandHandler]
    public function handle(RegisterUser $command): void
    {
        User::create(
            name: $command->name,
            email: $command->email,
            password: $this->passwordHasher->hash($command->password),
            created_at: DateTime::now(),
            updated_at: DateTime::now(),
        );
    }
}
