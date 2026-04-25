<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\PasswordResetMail;
use App\Models\PasswordReset;
use App\Models\User;
use App\Session\AppSessionManager;
use Tempest\Auth\Authentication\Authenticator;
use Tempest\Cryptography\Password\PasswordHasher;
use Tempest\DateTime\DateTime;
use Tempest\DateTime\Duration;
use Tempest\Mail\Mailer;

final readonly class AuthService
{
    public function __construct(
        private Authenticator $authenticator,
        private PasswordHasher $passwordHasher,
        private Mailer $mailer,
        private AppSessionManager $sessionManager,
    ) {}

    public function register(string $name, string $email, string $password): User
    {
        return User::create(
            name: $name,
            email: $email,
            password: $this->passwordHasher->hash($password),
            created_at: DateTime::now(),
            updated_at: DateTime::now(),
        );
    }

    public function login(string $email, string $password): bool
    {
        $user = User::find(email: $email)
            ->include('password')
            ->first();

        if (! $user) {
            return false;
        }

        if (! $this->passwordHasher->verify($password, $user->password)) {
            return false;
        }

        $this->authenticator->authenticate($user);

        return true;
    }

    public function sendPasswordResetEmail(string $email): void
    {
        $user = User::find(email: $email)->first();

        if ($user === null) {
            return;
        }

        $reset = PasswordReset::updateOrCreate(
            find: ['email' => $email],
            update: [
                'token' => bin2hex(random_bytes(32)),
                'created_at' => DateTime::now(),
            ],
        );

        $this->mailer->send(new PasswordResetMail($user, $reset->token));
    }

    public function resetPassword(string $token, string $password): bool
    {
        $reset = PasswordReset::find(token: $token)->first();

        if ($reset === null) {
            return false;
        }

        $expired = DateTime::now()->getTimestamp()->getSeconds() > $reset->created_at->plus(Duration::hours(1))->getTimestamp()->getSeconds();

        if ($expired) {
            $reset->delete();

            return false;
        }

        $user = User::find(email: $reset->email)->first();

        if ($user === null) {
            return false;
        }

        $user->update(password: $this->passwordHasher->hash($password));
        $reset->delete();

        return true;
    }

    public function logout(): void
    {
        $this->sessionManager->destroySession();
    }
}
