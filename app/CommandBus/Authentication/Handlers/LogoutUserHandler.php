<?php

declare(strict_types=1);

namespace App\CommandBus\Authentication\Handlers;

use App\CommandBus\Authentication\LogoutUser;
use Tempest\Auth\Authentication\Authenticator;
use Tempest\CommandBus\CommandHandler;

final readonly class LogoutUserHandler
{
    public function __construct(
        private Authenticator $authenticator,
    ) {}

    #[CommandHandler]
    public function handle(LogoutUser $command): void
    {
        $this->authenticator->deauthenticate();
    }
}
