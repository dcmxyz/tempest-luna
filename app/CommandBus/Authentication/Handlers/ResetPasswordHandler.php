<?php

declare(strict_types=1);

namespace App\CommandBus\Authentication\Handlers;

use App\CommandBus\Authentication\ResetPassword;
use App\Models\PasswordReset;
use App\Models\User;
use Tempest\CommandBus\CommandHandler;
use Tempest\DateTime\DateTime;
use Tempest\DateTime\Duration;

final readonly class ResetPasswordHandler
{
    #[CommandHandler]
    public function __invoke(ResetPassword $command): bool
    {
        $reset = PasswordReset::find(token: $command->token)->first();

        if ($reset === null) {
            return false;
        }

        $expired = DateTime::now()->getTimestamp()->getSeconds()
            > $reset->created_at->plus(Duration::hours(1))->getTimestamp()->getSeconds();

        if ($expired) {
            $reset->delete();
            return false;
        }

        $user = User::find(email: $reset->email)->first();

        if ($user === null) {
            return false;
        }

        $user->update(password: password_hash($command->password, PASSWORD_BCRYPT));
        $reset->delete();

        return true;
    }
}