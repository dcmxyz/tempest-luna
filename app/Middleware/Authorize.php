<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Models\User;
use App\Services\AuthService;
use Tempest\Auth\Authentication\Authenticator;
use Tempest\Http\Request;
use Tempest\Http\Response;
use Tempest\Router\HttpMiddleware;
use Tempest\Router\HttpMiddlewareCallable;

final readonly class Authorize implements HttpMiddleware
{
    public function __construct(
        private Authenticator $authenticator,
        private AuthService $authService,
    ) {}

    public function __invoke(Request $request, HttpMiddlewareCallable $next): Response
    {
        if ($this->authenticator->current() === null) {
            $cookie = $request->cookies[AuthService::COOKIE_REMEMBER_TOKEN] ?? null;

            if ($cookie !== null) {
                $this->attemptRememberLogin($cookie->value);
            }
        }

        return $next($request);
    }

    private function attemptRememberLogin(string $cookieValue): void
    {
        $parts = explode(':', $cookieValue, 2);

        if (count($parts) !== 2) {
            return;
        }

        [$userId, $validator] = $parts;

        $user = User::find(id: $userId)
            ->include('remember_token')
            ->first();

        if ($user === null || $user->remember_token === null) {
            return;
        }

        if (! hash_equals($user->remember_token, hash('sha256', $validator))) {
            return;
        }

        $this->authenticator->authenticate($user);
        $this->authService->rotateRememberToken($user);
    }
}
