<?php

declare(strict_types=1);

namespace Tests\Controllers\Authentication;

use App\Controllers\AccountController;
use App\Controllers\Authentication\VerifyEmailController;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tempest\Auth\Authentication\Authenticator;
use Tempest\Cryptography\Password\PasswordHasher;
use Tempest\DateTime\DateTime;
use Tempest\DateTime\Duration;
use Tempest\Mail\Mailer;
use Tempest\Mail\Testing\MailTester;
use Tempest\Mail\Testing\TestingMailer;
use Tempest\Router\UriGenerator;
use Tests\IntegrationTestCase;

use function Tempest\Router\uri;

/**
 * @internal
 */
final class VerifyEmailControllerTest extends IntegrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->database->setup();

        $testingMailer = new TestingMailer();
        $this->container->singleton(Mailer::class, fn () => $testingMailer);
        $this->mailer = new MailTester($testingMailer);
    }

    #[Test]
    public function verify_endpoint_requires_authentication(): void
    {
        $this->http->get('/account/email/verify/some-id')
            ->assertRedirect('/login');
    }

    #[Test]
    public function verify_with_valid_signature_marks_email_as_verified(): void
    {
        $user = $this->createUser('john@luna.com');
        $this->container->get(Authenticator::class)->authenticate($user);

        $verifyUri = $this->generateVerifyUri($user);

        $this->http->get($verifyUri)
            ->assertRedirect(uri([AccountController::class, 'index']));

        $updated = User::find(id: $user->id)->first();
        $this->assertNotNull($updated->email_verified_at);
    }

    #[Test]
    public function verify_with_invalid_signature_redirects_to_account(): void
    {
        $user = $this->createUser('john@luna.com');
        $this->container->get(Authenticator::class)->authenticate($user);

        $this->http->get('/account/email/verify/' . $user->id . '?signature=invalidsignature')
            ->assertRedirect(uri([AccountController::class, 'index']));
    }

    #[Test]
    public function verify_redirects_when_id_does_not_match_authenticated_user(): void
    {
        $user = $this->createUser('john@luna.com');
        $otherUser = $this->createUser('jane@luna.com');

        $this->container->get(Authenticator::class)->authenticate($user);

        $verifyUri = $this->generateVerifyUri($otherUser);

        $this->http->get($verifyUri)
            ->assertRedirect(uri([AccountController::class, 'index']));

        $updated = User::find(id: $otherUser->id)->first();
        $this->assertNull($updated->email_verified_at);
    }

    #[Test]
    public function resend_requires_authentication(): void
    {
        $this->http->post('/account/email/verify/resend')->assertRedirect('/login');
    }

    #[Test]
    public function resend_sends_verification_email(): void
    {
        $user = $this->createUser('john@luna.com');
        $this->container->get(Authenticator::class)->authenticate($user);

        $this->http->post('/account/email/verify/resend')->assertRedirect(uri([AccountController::class, 'index']));

        $this->mailer->assertSent(\App\Mail\EmailAddressConfirmMail::class);
    }

    #[Test]
    public function resend_is_rate_limited_by_cache(): void
    {
        $user = $this->createUser('john@luna.com');
        $this->container->get(Authenticator::class)->authenticate($user);

        $this->http->post('/account/email/verify/resend');

        $sentCount = count($this->mailer->mailer->sent ?? []);

        $this->http->post('/account/email/verify/resend');

        $this->assertCount($sentCount, $this->mailer->mailer->sent ?? []);
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

    private function generateVerifyUri(User $user): string
    {
        $uriGenerator = $this->container->get(UriGenerator::class);

        return $uriGenerator->createTemporarySignedUri(
            action: [VerifyEmailController::class, 'verify'],
            duration: Duration::minutes(15),
            id: $user->id,
        );
    }
}
