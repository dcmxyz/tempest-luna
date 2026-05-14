<?php

declare(strict_types=1);

namespace Tests\Controllers\Authentication;

use App\Controllers\Authentication\RegisterController;
use App\Mail\EmailAddressConfirmMail;
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
final class RegisterControllerTest extends IntegrationTestCase
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
    public function register_page_is_accessible_to_guests(): void
    {
        $this->inertia(uri([RegisterController::class, 'show']))
            ->assertOk();
    }

    #[Test]
    public function register_page_redirects_authenticated_users(): void
    {
        $user = $this->createUser('john@luna.com');
        $this->container->get(Authenticator::class)->authenticate($user);

        $this->http->get(uri([RegisterController::class, 'show']))
            ->assertRedirect('/');
    }

    #[Test]
    public function successful_registration_creates_user_and_redirects(): void
    {
        $this->http
            ->post('/register', [
                'name' => 'Jane Doe',
                'email' => 'jane@luna.com',
                'password' => 'Password123!',
            ])
            ->assertRedirect('/');

        $this->database->assertTableHasRow('users', email: 'jane@luna.com');
    }

    #[Test]
    public function successful_registration_sends_verification_email(): void
    {
        $this->http->post('/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@luna.com',
            'password' => 'Password123!',
        ]);

        $this->mailer->assertSent(
            EmailAddressConfirmMail::class,
            fn (EmailAddressConfirmMail $mail) => $mail->envelope->to === 'jane@luna.com',
        );
    }

    #[Test]
    public function registration_with_duplicate_email_returns_validation_error(): void
    {
        $this->createUser('jane@luna.com');

        $this->http
            ->post('/register', [
                'name' => 'Another Jane',
                'email' => 'jane@luna.com',
                'password' => 'password',
            ])
            ->assertHasValidationError('email');
    }

    #[Test]
    public function registration_with_name_too_short_returns_validation_error(): void
    {
        $this->http
            ->post('/register', [
                'name' => 'J',
                'email' => 'jane@luna.com',
                'password' => 'password',
            ])
            ->assertHasValidationError('name');
    }

    #[Test]
    public function registration_with_invalid_email_returns_validation_error(): void
    {
        $this->http
            ->post('/register', [
                'name' => 'Jane Doe',
                'email' => 'not-an-email',
                'password' => 'password',
            ])
            ->assertHasValidationError('email');
    }

    #[Test]
    public function registration_with_weak_password_returns_validation_error(): void
    {
        $this->http
            ->post('/register', [
                'name' => 'Jane Doe',
                'email' => 'jane@luna.com',
                'password' => 'weakpassword',
            ])
            ->assertHasValidationError('password');
    }

    #[Test]
    public function registration_with_password_too_short_returns_validation_error(): void
    {
        $this->http
            ->post('/register', [
                'name' => 'Jane Doe',
                'email' => 'jane@luna.com',
                'password' => 'Error1!',
            ])
            ->assertHasValidationError('password');
    }

    #[Test]
    public function registered_user_is_authenticated_when_login_after_register_is_enabled(): void
    {
        $this->http->post('/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@luna.com',
            'password' => 'Password123!',
        ]);

        $current = $this->container->get(Authenticator::class)->current();
        $this->assertNotNull($current);
        $this->assertSame('jane@luna.com', $current->email);
    }

    private function createUser(string $email): User
    {
        $hasher = $this->container->get(PasswordHasher::class);

        return User::create(
            name: 'Test User',
            email: $email,
            password: $hasher->hash('password'),
            created_at: DateTime::now(),
            updated_at: DateTime::now(),
        );
    }
}
