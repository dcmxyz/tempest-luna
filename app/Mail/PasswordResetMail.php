<?php

declare(strict_types=1);

namespace App\Mail;

use App\Controllers\Authentication\ForgotPasswordController;
use App\Controllers\Authentication\ResetPasswordController;
use App\Models\User;
use Tempest\Mail\Email;
use Tempest\Mail\Envelope;
use Tempest\View\View;

use function Tempest\root_path;
use function Tempest\Router\uri;
use function Tempest\View\view;

final class PasswordResetMail implements Email
{
    public function __construct(
        private readonly User $user,
        private readonly string $token,
    ) {}

    public Envelope $envelope {
        get => new Envelope(
            subject: 'Reset your password',
            to: $this->user->email,
        );
    }

    public string|View $html {
        get => view(
            root_path('app/Resources/mails') . '/password-reset.view.php',
            user: $this->user,
            resetUrl: uri([ResetPasswordController::class, 'show'], token: $this->token),
        );
    }
}
