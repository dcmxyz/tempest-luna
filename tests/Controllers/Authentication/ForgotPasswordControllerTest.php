<?php

declare(strict_types=1);

namespace Tests\Controllers\Authentication;

use App\Controllers\Authentication\ForgotPasswordController;
use App\Mail\PasswordResetMail;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tempest\Auth\Authentication\Authenticator;
use Tempest\Cryptography\Password\PasswordHasher;
use Tempest\DateTime\DateTime;
use Tempest\Mail\Mailer;
use Tempest\Mail\Testing\MailTester;
use Tempest\Mail\Testing\TestingMailer;
use Tests\IntegrationTestCase;
use Tests\Support\MakesInertiaRequests;

use function Tempest\Router\uri;

/**
 * @internal
 */
final class ForgotPasswordControllerTest extends IntegrationTestCase
{
    use MakesInertiaRequests;

    protected function setUp(): void
    {
        parent::setUp();

        $this->database->setup();

        $testingMailer = new TestingMailer();
        $this->container->singleton(Mailer::class, fn () => $testingMailer);
        $this->mailer = new MailTester($testingMailer);
    }

    #[Test]
    public function forgot_password_page_is_accessible_to_guests(): void
    {
        $this->inertia(uri([ForgotPasswordController::class, 'show']))
            ->assertOk();
    }

    #[Test]
    public function forgot_password_page_redirects_authenticated_users(): void
    {
        $user = $this->createUser('john@example.com');
        $this->container->get(Authenticator::class)->authenticate($user);

        $this->http->get(uri([ForgotPasswordController::class, 'show']))
            ->assertRedirect('/');
    }

    #[Test]
    public function submitting_known_email_sends_password_reset_email(): void
    {
        $this->createUser('john@luna.com');

        $this->http->post('/forgot-password', ['email' => 'john@luna.com'])->assertRedirect();

        $this->mailer->assertSent(
            PasswordResetMail::class,
            fn (PasswordResetMail $mail) => $mail->envelope->to === 'john@luna.com',
        );
    }

    #[Test]
    public function submitting_known_email_creates_password_reset_record(): void
    {
        $this->createUser('john@luna.com');

        $this->http->post('/forgot-password', ['email' => 'john@luna.com']);

        $this->database->assertTableHasRow('password_resets', email: 'john@luna.com');
    }

    #[Test]
    public function submitting_unknown_email_does_not_send_email(): void
    {
        $this->http->post('/forgot-password', ['email' => 'nobody@luna.com'])->assertRedirect();

        $this->mailer->assertNotSent(PasswordResetMail::class);
    }

    #[Test]
    public function submitting_unknown_email_still_redirects_without_revealing_existence(): void
    {
        $this->http
            ->post('/forgot-password', ['email' => 'nobody@luna.com'])
            ->assertRedirect(uri([ForgotPasswordController::class, 'show']));
    }

    #[Test]
    public function submitting_invalid_email_format_returns_validation_error(): void
    {
        $this->http->post('/forgot-password', ['email' => 'not-an-email'])->assertHasValidationError('email');
    }

    #[Test]
    public function submitting_missing_email_returns_validation_error(): void
    {
        $this->http->post('/forgot-password', [])->assertHasValidationError('email');
    }

    private function createUser(string $email): User
    {
        $hasher = $this->container->get(PasswordHasher::class);

        return User::create(
            name: 'Test User',
            email: $email,
            password: $hasher->hash('Password123secret'),
            created_at: DateTime::now(),
            updated_at: DateTime::now(),
        );
    }
}
