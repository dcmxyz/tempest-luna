<?php

declare(strict_types=1);

namespace App\CommandBus\Authentication\Handlers;

use App\CommandBus\Authentication\PasswordResetEmail;
use App\Mail\PasswordResetMail;
use App\Models\PasswordReset;
use App\Models\User;
use Tempest\CommandBus\CommandHandler;
use Tempest\DateTime\DateTime;
use Tempest\Mail\Mailer;

final readonly class PasswordResetEmailHandler
{
    public function __construct(
        private Mailer $mailer,
    ) {}

    #[CommandHandler]
    public function __invoke(PasswordResetEmail $command): void
    {
        $user = User::find(email: $command->email)
            ->first();

        if ($user === null) {
            return;
        }

        $reset = PasswordReset::updateOrCreate(
            find: ['email' => $command->email],
            update: [
                'token' => bin2hex(random_bytes(32)),
                'created_at' => DateTime::now(),
            ],
        );

        $this->mailer->send(email: new PasswordResetMail($user, $reset->token));
    }
}