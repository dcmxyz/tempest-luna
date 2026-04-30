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
use Tempest\Http\Cookie\CookieManager;
use Tempest\Mail\Mailer;

final readonly class AuthService
{
    public const string COOKIE_REMEMBER_TOKEN = 'luna_remember_token';

    public function __construct(
        private Authenticator $authenticator,
        private PasswordHasher $passwordHasher,
        private Mailer $mailer,
        private AppSessionManager $sessionManager,
        private CookieManager $cookieManager,
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

    public function login(string $email, string $password, bool $remember = false): bool
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

        if ($remember) {
            $this->issueRememberToken($user);
        }

        return true;
    }

    private function issueRememberToken(User $user): void
    {
        $token = $this->generateRandomToken(length: 64);

        $user->update(remember_token: hash('sha256', $token));

        $this->cookieManager->set(
            key: self::COOKIE_REMEMBER_TOKEN,
            value: $user->id . ':' . $token,
            expiresAt: DateTime::now()->plus(Duration::days(30)),
        );
    }

    public function rotateRememberToken(User $user): void
    {
        $this->issueRememberToken($user);
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
                'token' => $this->generateRandomToken(length: 32),
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

        $expired = DateTime::now()->getTimestamp()->getSeconds() > $reset
            ->created_at
            ->plus(Duration::hours(1))
            ->getTimestamp()
            ->getSeconds();

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
        $authenticatedUser = $this->authenticator->current();
        $authenticatedUser?->update(remember_token: null);

        $this->cookieManager->remove(self::COOKIE_REMEMBER_TOKEN);

        $this->sessionManager->destroySession();
    }

    private function generateRandomToken(int $length): string
    {
        $token = '';

        while (strlen($token) < $length) {
            $needed = $length - strlen($token);

            $bytes = random_bytes((int) ceil($needed / 3) * 3);

            $token .= $bytes
                |> base64_encode(...)
                |> (static fn ($subject) => str_replace(['/', '+', '='], '', $subject))
                |> (static fn ($x) => substr($x, 0, $needed));
        }

        return $token;
    }
}
