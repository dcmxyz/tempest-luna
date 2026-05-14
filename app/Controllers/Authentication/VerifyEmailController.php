<?php

declare(strict_types=1);

namespace App\Controllers\Authentication;

use App\Controllers\AccountController;
use App\Middleware\MustBeAuthenticated;
use App\Models\User;
use App\Services\AuthService;
use Tempest\Auth\Authentication\Authenticator;
use Tempest\Cache\Cache;
use Tempest\DateTime\Duration;
use Tempest\Http\Request;
use Tempest\Http\Responses\Redirect;
use Tempest\Router\Get;
use Tempest\Router\Post;
use Tempest\Router\UriGenerator;

use function Tempest\Router\uri;

final readonly class VerifyEmailController
{
    public function __construct(
        private AuthService $authService,
        private Authenticator $authenticator,
        private Cache $cache,
        private UriGenerator $uriGenerator,
    ) {}

    #[Get('/account/email/verify/{id}', middleware: [MustBeAuthenticated::class])]
    public function verify(string $id, Request $request): Redirect
    {
        if (! $this->uriGenerator->hasValidSignature($request)) {
            return new Redirect(uri([AccountController::class, 'index']));
        }

        if ((string) $this->authenticator->current()?->id !== $id) {
            return new Redirect(uri([AccountController::class, 'index']));
        }

        $user = User::find(id: $id)->first();

        if ($user === null) {
            return new Redirect(uri([AccountController::class, 'index']));
        }

        $this->authService->verifyEmail($user);

        return new Redirect(uri([AccountController::class, 'index']));
    }

    #[Post('/account/email/verify/resend', middleware: [MustBeAuthenticated::class])]
    public function resend(): Redirect
    {
        $authenticated = $this->authenticator->current();
        $cacheKey = 'email-verification-sent-' . md5((string) $authenticated->id);

        if ($this->cache->get($cacheKey) !== null) {
            return new Redirect(uri([AccountController::class, 'index']));
        }

        $this->cache->put($cacheKey, true, Duration::seconds(30));

        $this->authService->sendVerificationEmail($authenticated);

        return new Redirect(uri([AccountController::class, 'index']));
    }
}
