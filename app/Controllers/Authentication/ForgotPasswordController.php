<?php

declare(strict_types=1);

namespace App\Controllers\Authentication;

use App\Requests\Authentication\ForgotPasswordRequest;
use App\Services\AuthService;
use Inertia\Response;
use Tempest\Http\Responses\Redirect;
use Tempest\Router\Get;
use Tempest\Router\Post;

use function Tempest\Router\uri;

final readonly class ForgotPasswordController
{
    public function __construct(
        private AuthService $authService,
    ) {}

    #[Get('/forgot-password')]
    public function show(): Response
    {
        return inertia(component: 'Authentication/ForgotPassword');
    }

    #[Post('/forgot-password')]
    public function send(ForgotPasswordRequest $request): Redirect
    {
        $this->authService->sendPasswordResetEmail(
            email: $request->email,
        );

        return new Redirect(uri([self::class, 'show']));
    }
}