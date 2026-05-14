<?php

declare(strict_types=1);

namespace Tests\Controllers\Authentication;

use App\Controllers\Authentication\LoginController;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tempest\Auth\Authentication\Authenticator;
use Tempest\Cryptography\Password\PasswordHasher;
use Tempest\DateTime\DateTime;
use Tests\IntegrationTestCase;
use Tests\Support\MakesInertiaRequests;

use function Tempest\Router\uri;

/**
 * @internal
 */
final class LoginControllerTest extends IntegrationTestCase
{
    use MakesInertiaRequests;

    protected function setUp(): void
    {
        parent::setUp();

        $this->database->setup();
    }

    #[Test]
    public function login_page_is_accessible_to_guests(): void
    {
        $this->inertia(uri([LoginController::class, 'show']))
            ->assertOk();
    }

    #[Test]
    public function login_page_redirects_authenticated_users(): void
    {
        $user = $this->createUser('john@luna.com');
        $this->container->get(Authenticator::class)->authenticate($user);

        $this->http->get(uri([LoginController::class, 'show']))
            ->assertRedirect('/');
    }

    #[Test]
    public function successful_login_redirects_to_home(): void
    {
        $this->createUser('john@luna.com', 'password');

        $this->http
            ->post('/login', [
                'email' => 'john@luna.com',
                'password' => 'password',
            ])
            ->assertRedirect('/');
    }

    #[Test]
    public function login_with_wrong_password_returns_validation_error(): void
    {
        $this->createUser('john@luna.com', 'password');

        $this->http
            ->post('/login', [
                'email' => 'john@luna.com',
                'password' => 'wrong-password',
            ])
            ->assertHasValidationError('email');
    }

    #[Test]
    public function login_with_unknown_email_returns_validation_error(): void
    {
        $this->http
            ->post('/login', [
                'email' => 'nobody@luna.com',
                'password' => 'password',
            ])
            ->assertHasValidationError('email');
    }

    #[Test]
    public function login_with_missing_email_returns_validation_error(): void
    {
        $this->http
            ->post('/login', [
                'password' => 'password',
            ])
            ->assertHasValidationError('email');
    }

    #[Test]
    public function login_with_invalid_email_format_returns_validation_error(): void
    {
        $this->http
            ->post('/login', [
                'email' => 'not-an-email',
                'password' => 'password',
            ])
            ->assertHasValidationError('email');
    }

    #[Test]
    public function login_with_missing_password_returns_validation_error(): void
    {
        $this->http
            ->post('/login', [
                'email' => 'john@luna.com',
            ])
            ->assertHasValidationError('password');
    }

    #[Test]
    public function authenticated_user_cannot_submit_login_form(): void
    {
        $user = $this->createUser('john@luna.com');
        $this->container->get(Authenticator::class)->authenticate($user);

        $this->http
            ->post('/login', [
                'email' => 'john@luna.com',
                'password' => 'password',
            ])
            ->assertRedirect('/');
    }

    private function createUser(string $email, string $password = 'password'): User
    {
        $hasher = $this->container->get(PasswordHasher::class);

        return User::create(
            name: 'Test User',
            email: $email,
            password: $hasher->hash($password),
            created_at: DateTime::now(),
            updated_at: DateTime::now(),
        );
    }
}
