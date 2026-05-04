<?php

declare(strict_types=1);

namespace App\Services;

use App\Controllers\Authentication\VerifyEmailController;
use App\Mail\EmailAddressConfirmMail;
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
use Tempest\Router\UriGenerator;

final readonly class AuthService
{
    public const string COOKIE_REMEMBER_TOKEN = 'luna_remember_token';

    public function __construct(
        private Authenticator $authenticator,
        private PasswordHasher $passwordHasher,
        private Mailer $mailer,
        private AppSessionManager $sessionManager,
        private CookieManager $cookieManager,
        private UriGenerator $uriGenerator,
    ) {}

    public function register(string $name, string $email, #[\SensitiveParameter] string $password): User
    {
        return User::create(
            name: $name,
            email: $email,
            password: $this->passwordHasher->hash($password),
            created_at: DateTime::now(),
            updated_at: DateTime::now(),
        );
    }

    public function login(string $email, #[\SensitiveParameter] string $password, bool $remember = false): bool
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

        $rawToken = $this->generateRandomToken(length: 32);

        PasswordReset::updateOrCreate(
            find: ['email' => $email],
            update: [
                'token' => hash('sha256', $rawToken),
                'created_at' => DateTime::now(),
            ],
        );

        $this->mailer->send(new PasswordResetMail($user, $rawToken));
    }

    public function resetPassword(#[\SensitiveParameter] string $token, #[\SensitiveParameter] string $password): bool
    {
        $reset = PasswordReset::find(token: hash('sha256', $token))->first();

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

    public function sendVerificationEmail(User $user): void
    {
        if ($user->email_verified_at !== null) {
            return;
        }

        $uri = $this->uriGenerator->createTemporarySignedUri(
            action: [VerifyEmailController::class, 'verify'],
            duration: Duration::minutes(15),
            id: $user->id,
        );

        $this->mailer->send(new EmailAddressConfirmMail($user, $uri));
    }

    public function verifyEmail(User $user): void
    {
        $user->update(email_verified_at: DateTime::now());
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
