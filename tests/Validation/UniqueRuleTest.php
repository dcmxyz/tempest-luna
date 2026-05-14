<?php

declare(strict_types=1);

namespace Tests\Validation;

use App\Models\User;
use App\Validation\Unique;
use App\Validation\UniqueEmailExcluding;
use PHPUnit\Framework\Attributes\Test;
use Tempest\Cryptography\Password\PasswordHasher;
use Tempest\DateTime\DateTime;
use Tests\IntegrationTestCase;

/**
 * @internal
 */
final class UniqueRuleTest extends IntegrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->database->setup();
    }

    #[Test]
    public function unique_passes_when_value_does_not_exist(): void
    {
        $rule = new Unique(User::class, 'email');

        $this->assertTrue($rule->isValid('test@luna.com'));
    }

    #[Test]
    public function unique_fails_when_value_exists(): void
    {
        $this->createUser('exists@luna.com');

        $rule = new Unique(User::class, 'email');

        $this->assertFalse($rule->isValid('exists@luna.com'));
    }

    #[Test]
    public function unique_email_excluding_passes_for_unused_email(): void
    {
        $user = $this->createUser('user@luna.com');

        $rule = new UniqueEmailExcluding(excludeId: $user->id);

        $this->assertTrue($rule->isValid('other@luna.com'));
    }

    #[Test]
    public function unique_email_excluding_passes_for_own_email(): void
    {
        $user = $this->createUser('user@luna.com');

        $rule = new UniqueEmailExcluding(excludeId: $user->id);

        $this->assertTrue($rule->isValid('user@luna.com'));
    }

    #[Test]
    public function unique_email_excluding_fails_when_email_belongs_to_another_user(): void
    {
        $this->createUser('exists@luna.com');
        $user = $this->createUser('user@luna.com');

        $rule = new UniqueEmailExcluding(excludeId: $user->id);

        $this->assertFalse($rule->isValid('exists@luna.com'));
    }

    #[Test]
    public function unique_email_excluding_has_error_message(): void
    {
        $user = $this->createUser('user@luna.com');

        $rule = new UniqueEmailExcluding(excludeId: $user->id);

        $this->assertSame('This email is already in use.', $rule->getErrorMessage());
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
