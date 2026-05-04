<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Controllers\DashboardController;
use Tempest\Auth\Authentication\Authenticator;
use Tempest\Discovery\SkipDiscovery;
use Tempest\Http\Request;
use Tempest\Http\Response;
use Tempest\Http\Responses\Redirect;
use Tempest\Router\HttpMiddleware;
use Tempest\Router\HttpMiddlewareCallable;

use function Tempest\Router\uri;

#[SkipDiscovery]
final readonly class MustHaveVerifiedEmail implements HttpMiddleware
{
    public function __construct(private Authenticator $authenticator) {}

    public function __invoke(Request $request, HttpMiddlewareCallable $next): Response
    {
        $user = $this->authenticator->current();

        if ($user?->email_verified_at === null) {
            return new Redirect(uri([DashboardController::class, '__invoke']));
        }

        return $next($request);
    }
}