<?php

declare(strict_types=1);

namespace Tests\Controllers\Authentication;

use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tempest\Auth\Authentication\Authenticator;
use Tempest\Cryptography\Password\PasswordHasher;
use Tempest\DateTime\DateTime;
use Tests\IntegrationTestCase;

/**
 * @internal
 */
final class LogoutControllerTest extends IntegrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->database->setup();
    }

    #[Test]
    public function logout_redirects_unauthenticated_users_to_login(): void
    {
        $this->http->post('/logout')->assertRedirect('/login');
    }

    #[Test]
    public function logout_redirects_authenticated_user_to_home(): void
    {
        $user = $this->createUser('john@luna.com');
        $this->container->get(Authenticator::class)->authenticate($user);

        $this->http->post('/logout')->assertRedirect('/');
    }

    #[Test]
    public function logout_clears_the_remember_token(): void
    {
        $hasher = $this->container->get(PasswordHasher::class);
        $user = User::create(
            name: 'John',
            email: 'john@luna.com',
            password: $hasher->hash('password'),
            created_at: DateTime::now(),
            updated_at: DateTime::now(),
        );
        $user->update(remember_token: hash('sha256', 'sometoken'));

        $this->container->get(Authenticator::class)->authenticate($user);

        $this->http->post('/logout');

        $updated = User::find(id: $user->id)->include('remember_token')->first();
        $this->assertNull($updated->remember_token);
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
