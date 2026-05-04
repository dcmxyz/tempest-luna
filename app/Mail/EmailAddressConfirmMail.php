<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Tempest\Mail\Email;
use Tempest\Mail\Envelope;
use Tempest\View\View;

use function Tempest\root_path;
use function Tempest\View\view;

final class EmailAddressConfirmMail implements Email
{
    public function __construct(
        private readonly User $user,
        #[\SensitiveParameter]
        private readonly string $uri,
    ) {}

    public Envelope $envelope {
        get => new Envelope(
            subject: 'Confirm your email address',
            to: $this->user->email,
        );
    }

    public string|View $html {
        get => view(
            root_path('app/Resources/mails') . '/email-confirm.view.php',
            user: $this->user,
            verificationUri: $this->uri,
        );
    }
}