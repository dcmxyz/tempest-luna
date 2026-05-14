<?php

declare(strict_types=1);

namespace Tests\Controllers;

use App\Controllers\AccountController;
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
final class AccountControllerTest extends IntegrationTestCase
{
    use MakesInertiaRequests;

    private PasswordHasher $hasher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->database->setup();

        $testingMailer = new TestingMailer();
        $this->container->singleton(Mailer::class, fn () => $testingMailer);
        $this->mailer = new MailTester($testingMailer);

        $this->hasher = $this->container->get(PasswordHasher::class);
    }

    #[Test]
    public function account_page_requires_authentication(): void
    {
        $this->http->get(uri([AccountController::class, 'index']))
            ->assertRedirect('/login');
    }

    #[Test]
    public function account_page_is_accessible_when_authenticated(): void
    {
        $user = $this->createUser('john@luna.com');
        $this->container->get(Authenticator::class)->authenticate($user);

        $this->inertia(uri([AccountController::class, 'index']))
            ->assertOk();
    }

    #[Test]
    public function update_name_changes_the_users_name(): void
    {
        $user = $this->createUser('john@luna.com');
        $this->container->get(Authenticator::class)->authenticate($user);

        $this->http->post('/account/name', ['name' => 'John Updated'])->assertRedirect('/account');

        $this->database->assertTableHasRow('users', name: 'John Updated');
    }

    #[Test]
    public function update_name_with_too_short_name_returns_validation_error(): void
    {
        $user = $this->createUser('john@luna.com');
        $this->container->get(Authenticator::class)->authenticate($user);

        $this->http->post('/account/name', ['name' => 'J'])->assertHasValidationError('name');
    }

    #[Test]
    public function update_name_requires_authentication(): void
    {
        $this->http->post('/account/name', ['name' => 'John Updated'])->assertRedirect('/login');
    }

    #[Test]
    public function update_email_changes_the_users_email(): void
    {
        $user = $this->createUser('john@luna.com');
        $this->container->get(Authenticator::class)->authenticate($user);

        $this->http->post('/account/email', ['email' => 'newemail@luna.com'])->assertRedirect('/account');

        $this->database->assertTableHasRow('users', email: 'newemail@luna.com');
    }

    #[Test]
    public function update_email_clears_email_verified_at(): void
    {
        $user = $this->createUser('john@luna.com');
        $user->update(email_verified_at: DateTime::now());

        $this->container->get(Authenticator::class)->authenticate($user);

        $this->http->post('/account/email', ['email' => 'newemail@luna.com']);

        $updated = User::find(id: $user->id)->first();
        $this->assertNull($updated->email_verified_at);
    }

    #[Test]
    public function update_email_with_duplicate_email_returns_validation_error(): void
    {
        $this->createUser('taken@luna.com');

        $user = $this->createUser('john@luna.com');
        $this->container->get(Authenticator::class)->authenticate($user);

        $this->http->post('/account/email', ['email' => 'taken@luna.com'])->assertHasValidationError('email');
    }

    #[Test]
    public function update_email_allows_setting_own_email(): void
    {
        $user = $this->createUser('john@luna.com');
        $this->container->get(Authenticator::class)->authenticate($user);

        $this->http->post('/account/email', ['email' => 'john@luna.com'])->assertRedirect('/account');
    }

    #[Test]
    public function update_email_requires_authentication(): void
    {
        $this->http->post('/account/email', ['email' => 'new@luna.com'])->assertRedirect('/login');
    }

    #[Test]
    public function update_password_changes_the_password(): void
    {
        $user = $this->createUser('john@luna.com', 'OldPassword123');
        $this->container->get(Authenticator::class)->authenticate($user);

        $this->http
            ->post('/account/password', [
                'current_password' => 'OldPassword123',
                'password' => 'NewPassword123secret',
            ])
            ->assertRedirect('/account');

        $updated = User::find(id: $user->id)->include('password')->first();
        $this->assertTrue($this->hasher->verify('NewPassword123secret', $updated->password));
    }

    #[Test]
    public function update_password_with_wrong_current_password_returns_validation_error(): void
    {
        $user = $this->createUser('john@luna.com', 'OldPassword123');
        $this->container->get(Authenticator::class)->authenticate($user);

        $this->http
            ->post('/account/password', [
                'current_password' => 'WrongPassword999',
                'password' => 'NewPassword123secret',
            ])
            ->assertHasValidationError('current_password');
    }

    #[Test]
    public function update_password_with_weak_new_password_returns_validation_error(): void
    {
        $user = $this->createUser('john@luna.com', 'OldPassword123');
        $this->container->get(Authenticator::class)->authenticate($user);

        $this->http
            ->post('/account/password', [
                'current_password' => 'OldPassword123',
                'password' => 'weakpass',
            ])
            ->assertHasValidationError('password');
    }

    #[Test]
    public function update_password_requires_authentication(): void
    {
        $this->http
            ->post('/account/password', [
                'current_password' => 'OldPassword123',
                'password' => 'NewPassword123secret',
            ])
            ->assertRedirect('/login');
    }

    #[Test]
    public function delete_account_with_correct_password_deletes_user_and_redirects(): void
    {
        $user = $this->createUser('john@luna.com', 'Password123secret');
        $this->container->get(Authenticator::class)->authenticate($user);

        $this->http
            ->post('/account/delete', [
                'current_password' => 'Password123secret',
            ])
            ->assertRedirect('/');

        $this->database->assertTableDoesNotHaveRow('users', email: 'john@luna.com');
    }

    #[Test]
    public function delete_account_with_wrong_password_returns_validation_error(): void
    {
        $user = $this->createUser('john@luna.com', 'Password123secret');
        $this->container->get(Authenticator::class)->authenticate($user);

        $this->http
            ->post('/account/delete', [
                'current_password' => 'WrongPassword999',
            ])
            ->assertHasValidationError('current_password');

        $this->database->assertTableHasRow('users', email: 'john@luna.com');
    }

    #[Test]
    public function delete_account_requires_authentication(): void
    {
        $this->http
            ->post('/account/delete', [
                'current_password' => 'Password123secret',
            ])
            ->assertRedirect('/login');
    }

    #[Test]
    public function sessions_endpoint_requires_authentication(): void
    {
        $this->http->get(uri([AccountController::class, 'sessions']))
            ->assertRedirect('/login');
    }

    #[Test]
    public function sessions_endpoint_returns_json_for_authenticated_user(): void
    {
        $user = $this->createUser('john@luna.com');
        $this->container->get(Authenticator::class)->authenticate($user);

        $this->http->get(uri([AccountController::class, 'sessions']))
            ->assertOk();
    }

    #[Test]
    public function logout_all_sessions_requires_authentication(): void
    {
        $this->http->post(uri([AccountController::class, 'logoutAllSessions']))->assertRedirect('/login');
    }

    #[Test]
    public function logout_all_sessions_redirects_to_account(): void
    {
        $user = $this->createUser('john@luna.com');
        $this->container->get(Authenticator::class)->authenticate($user);

        $this->http->post(uri([AccountController::class, 'logoutAllSessions']))->assertRedirect('/account');
    }

    private function createUser(string $email, string $password = 'Password123secret'): User
    {
        return User::create(
            name: 'Test User',
            email: $email,
            password: $this->hasher->hash($password),
            created_at: DateTime::now(),
            updated_at: DateTime::now(),
        );
    }
}
