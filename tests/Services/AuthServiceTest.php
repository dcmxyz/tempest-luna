<?php

declare(strict_types=1);

namespace Tests\Services;

use App\Mail\EmailAddressConfirmMail;
use App\Mail\PasswordResetMail;
use App\Models\PasswordReset;
use App\Models\User;
use App\Services\AuthService;
use PHPUnit\Framework\Attributes\Test;
use Tempest\Auth\Authentication\Authenticator;
use Tempest\Cryptography\Password\PasswordHasher;
use Tempest\DateTime\DateTime;
use Tempest\DateTime\Duration;
use Tempest\Mail\Mailer;
use Tempest\Mail\Testing\MailTester;
use Tempest\Mail\Testing\TestingMailer;
use Tests\IntegrationTestCase;

/**
 * @internal
 */
final class AuthServiceTest extends IntegrationTestCase
{
    private AuthService $authService;

    private PasswordHasher $passwordHasher;

    private Authenticator $authenticator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->database->setup();

        $testingMailer = new TestingMailer();
        $this->container->singleton(Mailer::class, fn () => $testingMailer);
        $this->mailer = new MailTester($testingMailer);

        $this->authService = $this->container->get(AuthService::class);
        $this->passwordHasher = $this->container->get(PasswordHasher::class);
        $this->authenticator = $this->container->get(Authenticator::class);
    }

    #[Test]
    public function register_creates_a_user_in_the_database(): void
    {
        $user = $this->authService->register(
            name: 'Jane Doe',
            email: 'jane@luna.com',
            password: 'Password123secret',
        );

        $this->assertNotNull($user->id);
        $this->assertSame('Jane Doe', $user->name);
        $this->assertSame('jane@luna.com', $user->email);
        $this->database->assertTableHasRow('users', email: 'jane@luna.com');
    }

    #[Test]
    public function register_hashes_the_password(): void
    {
        $user = $this->authService->register(
            name: 'Jane Doe',
            email: 'jane@luna.com',
            password: 'Password123secret',
        );

        $stored = User::find(id: $user->id)->include('password')->first();

        $this->assertNotSame('Password123secret', $stored->password);
        $this->assertTrue($this->passwordHasher->verify('Password123secret', $stored->password));
    }

    #[Test]
    public function login_returns_true_and_authenticates_with_valid_credentials(): void
    {
        $this->createUser('john@luna.com', 'Password123secret');

        $result = $this->authService->login(
            email: 'john@luna.com',
            password: 'Password123secret',
        );

        $this->assertTrue($result);
        $this->assertNotNull($this->authenticator->current());
        $this->assertSame('john@luna.com', $this->authenticator->current()->email);
    }

    #[Test]
    public function login_returns_false_for_unknown_email(): void
    {
        $result = $this->authService->login(
            email: 'nobody@luna.com',
            password: 'Password123secret',
        );

        $this->assertFalse($result);
        $this->assertNull($this->authenticator->current());
    }

    #[Test]
    public function login_returns_false_for_wrong_password(): void
    {
        $this->createUser('john@luna.com', 'Password123secret');

        $result = $this->authService->login(
            email: 'john@luna.com',
            password: 'WrongPassword999',
        );

        $this->assertFalse($result);
        $this->assertNull($this->authenticator->current());
    }

    #[Test]
    public function send_password_reset_email_sends_email_for_existing_user(): void
    {
        $this->createUser('john@luna.com', 'Password123secret');

        $this->authService->sendPasswordResetEmail('john@luna.com');

        $this->mailer->assertSent(PasswordResetMail::class);
    }

    #[Test]
    public function send_password_reset_email_creates_a_password_reset_record(): void
    {
        $this->createUser('john@luna.com', 'Password123secret');

        $this->authService->sendPasswordResetEmail('john@luna.com');

        $this->database->assertTableHasRow('password_resets', email: 'john@luna.com');
    }

    #[Test]
    public function send_password_reset_email_does_nothing_for_unknown_email(): void
    {
        $this->authService->sendPasswordResetEmail('nobody@luna.com');

        $this->mailer->assertNotSent(PasswordResetMail::class);
        $this->database->assertTableEmpty('password_resets');
    }

    #[Test]
    public function reset_password_updates_password_with_valid_token(): void
    {
        $this->createUser('john@luna.com', 'OldPassword123');

        $rawToken = 'validtoken123456789012345678901';
        PasswordReset::create(
            email: 'john@luna.com',
            token: hash('sha256', $rawToken),
            created_at: DateTime::now(),
        );

        $result = $this->authService->resetPassword($rawToken, 'NewPassword123secret');

        $this->assertTrue($result);

        $user = User::find(email: 'john@luna.com')->include('password')->first();
        $this->assertTrue($this->passwordHasher->verify('NewPassword123secret', $user->password));
    }

    #[Test]
    public function reset_password_deletes_the_reset_record_on_success(): void
    {
        $this->createUser('john@luna.com', 'OldPassword123');

        $rawToken = 'validtoken123456789012345678901';
        PasswordReset::create(
            email: 'john@luna.com',
            token: hash('sha256', $rawToken),
            created_at: DateTime::now(),
        );

        $this->authService->resetPassword($rawToken, 'NewPassword123secret');

        $this->database->assertTableEmpty('password_resets');
    }

    #[Test]
    public function reset_password_returns_false_for_invalid_token(): void
    {
        $result = $this->authService->resetPassword('invalidtoken', 'NewPassword123secret');

        $this->assertFalse($result);
    }

    #[Test]
    public function reset_password_returns_false_and_deletes_expired_token(): void
    {
        $this->createUser('john@luna.com', 'OldPassword123');

        $rawToken = 'expiredtoken1234567890123456789';
        PasswordReset::create(
            email: 'john@luna.com',
            token: hash('sha256', $rawToken),
            created_at: DateTime::now()->minus(Duration::hours(2)),
        );

        $result = $this->authService->resetPassword($rawToken, 'NewPassword123secret');

        $this->assertFalse($result);
        $this->database->assertTableEmpty('password_resets');
    }

    #[Test]
    public function verify_email_sets_email_verified_at(): void
    {
        $user = $this->createUser('john@luna.com', 'Password123secret');

        $this->authService->verifyEmail($user);

        $updated = User::find(id: $user->id)->first();
        $this->assertNotNull($updated->email_verified_at);
    }

    #[Test]
    public function verify_email_is_idempotent_when_already_verified(): void
    {
        $user = $this->createUser('john@luna.com', 'Password123secret');
        $verifiedAt = DateTime::now()->minus(Duration::hours(1));
        $user->update(email_verified_at: $verifiedAt);

        $this->authService->verifyEmail($user);

        $updated = User::find(id: $user->id)->first();
        $this->assertSame(
            $verifiedAt->getTimestamp()->getSeconds(),
            $updated->email_verified_at->getTimestamp()->getSeconds(),
        );
    }

    #[Test]
    public function send_verification_email_sends_email_to_user(): void
    {
        $user = $this->createUser('john@luna.com', 'Password123secret');

        $this->authService->sendVerificationEmail($user);

        $this->mailer->assertSent(
            EmailAddressConfirmMail::class,
            fn (EmailAddressConfirmMail $mail) => $mail->envelope->to === 'john@luna.com',
        );
    }

    private function createUser(string $email, string $password): User
    {
        return User::create(
            name: 'Test User',
            email: $email,
            password: $this->passwordHasher->hash($password),
            created_at: DateTime::now(),
            updated_at: DateTime::now(),
        );
    }
}
