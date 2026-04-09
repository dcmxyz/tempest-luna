<?php

declare(strict_types=1);

namespace App\CommandBus\Authentication\Handlers;

use App\CommandBus\Authentication\LoginUser;
use App\Models\User;
use Tempest\Auth\Authentication\Authenticator;
use Tempest\CommandBus\CommandHandler;
use Tempest\Cryptography\Password\PasswordHasher;

final readonly class LoginUserHandler
{
    public function __construct(
        private Authenticator $authenticator,
        private PasswordHasher $passwordHasher,
    ) {}

    #[CommandHandler]
    public function handle(LoginUser $command): void
    {
        $user = User::find(email: $command->email)
            ->include('password')
            ->first();

        if (! $user || ! $this->passwordHasher->verify($command->password, $user->password)) {
            return;
        }

        $this->authenticator->authenticate($user);
    }
}
