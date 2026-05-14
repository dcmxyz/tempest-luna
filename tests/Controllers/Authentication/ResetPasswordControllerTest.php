<?php

declare(strict_types=1);

namespace Tests\Controllers\Authentication;

use App\Controllers\Authentication\LoginController;
use App\Models\PasswordReset;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tempest\Cryptography\Password\PasswordHasher;
use Tempest\DateTime\DateTime;
use Tempest\DateTime\Duration;
use Tests\IntegrationTestCase;
use Tests\Support\MakesInertiaRequests;

use function Tempest\Router\uri;

/**
 * @internal
 */
final class ResetPasswordControllerTest extends IntegrationTestCase
{
    use MakesInertiaRequests;

    protected function setUp(): void
    {
        parent::setUp();

        $this->database->setup();
    }

    #[Test]
    public function reset_password_page_shows_the_token(): void
    {
        $this
            ->inertia('/reset-password/mytoken123')
            ->assertOk()
            ->assertProp('token', 'mytoken123');
    }

    #[Test]
    public function successful_reset_redirects_to_login(): void
    {
        $this->createUser('john@luna.com');
        $rawToken = $this->createPasswordReset('john@luna.com');

        $this->http
            ->post('/reset-password', [
                'token' => $rawToken,
                'password' => 'NewPassword123secret',
            ])
            ->assertRedirect(uri([LoginController::class, 'show']));
    }

    #[Test]
    public function successful_reset_updates_the_password(): void
    {
        $user = $this->createUser('john@luna.com', 'OldPassword123');
        $rawToken = $this->createPasswordReset('john@luna.com');

        $this->http->post('/reset-password', [
            'token' => $rawToken,
            'password' => 'NewPassword123secret',
        ]);

        $updated = User::find(id: $user->id)->include('password')->first();
        $hasher = $this->container->get(PasswordHasher::class);
        $this->assertTrue($hasher->verify('NewPassword123secret', $updated->password));
    }

    #[Test]
    public function successful_reset_deletes_the_reset_record(): void
    {
        $this->createUser('john@luna.com');
        $rawToken = $this->createPasswordReset('john@luna.com');

        $this->http->post('/reset-password', [
            'token' => $rawToken,
            'password' => 'NewPassword123secret',
        ]);

        $this->database->assertTableEmpty('password_resets');
    }

    #[Test]
    public function reset_with_invalid_token_returns_validation_error(): void
    {
        $this->http
            ->post('/reset-password', [
                'token' => 'completelyinvalidtoken',
                'password' => 'NewPassword123secret',
            ])
            ->assertHasValidationError('password');
    }

    #[Test]
    public function reset_with_expired_token_returns_validation_error(): void
    {
        $this->createUser('john@luna.com');

        $rawToken = 'expiredtoken1234567890123456789';
        PasswordReset::create(
            email: 'john@luna.com',
            token: hash('sha256', $rawToken),
            created_at: DateTime::now()->minus(Duration::hours(2)),
        );

        $this->http
            ->post('/reset-password', [
                'token' => $rawToken,
                'password' => 'NewPassword123secret',
            ])
            ->assertHasValidationError('password');
    }

    #[Test]
    public function reset_with_weak_password_returns_validation_error(): void
    {
        $this->createUser('john@luna.com');
        $rawToken = $this->createPasswordReset('john@luna.com');

        $this->http
            ->post('/reset-password', [
                'token' => $rawToken,
                'password' => 'weakpassword',
            ])
            ->assertHasValidationError('password');
    }

    #[Test]
    public function reset_with_missing_token_returns_validation_error(): void
    {
        $this->http
            ->post('/reset-password', [
                'password' => 'NewPassword123secret',
            ])
            ->assertHasValidationError('token');
    }

    private function createUser(string $email, string $password = 'Password123secret'): User
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

    private function createPasswordReset(string $email): string
    {
        $rawToken = 'validtoken12345678901234567890a';

        PasswordReset::create(
            email: $email,
            token: hash('sha256', $rawToken),
            created_at: DateTime::now(),
        );

        return $rawToken;
    }
}
