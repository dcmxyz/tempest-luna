<?php

declare(strict_types=1);

namespace App\Controllers\Authentication;

use App\Middleware\RedirectIfAuthenticated;
use App\Requests\Authentication\ResetPasswordRequest;
use App\Services\AuthService;
use Inertia\Response;
use Inertia\ResponseFactory;
use Tempest\Http\Responses\Redirect;
use Tempest\Router\Get;
use Tempest\Router\Post;

use function Tempest\Router\uri;

final readonly class ResetPasswordController
{
    public function __construct(
        private AuthService $authService,
    ) {}

    #[Get('/reset-password/{token}', middleware: [RedirectIfAuthenticated::class])]
    public function show(#[\SensitiveParameter] string $token): Response
    {
        return inertia(component: 'Authentication/ResetPassword', props: ['token' => $token]);
    }

    #[Post('/reset-password', middleware: [RedirectIfAuthenticated::class])]
    public function store(ResetPasswordRequest $request): Response|ResponseFactory|Redirect
    {
        $passwordReset = $this->authService->resetPassword(
            token: $request->token,
            password: $request->password,
        );

        if (! $passwordReset) {
            return inertia('Authentication/ForgotPassword', [
                'errors' => ['email' => 'These credentials do not match our records.'],
            ]);
        }

        return new Redirect(uri([LoginController::class, 'show']));
    }
}
