<?php

declare(strict_types=1);

namespace App\Controllers\Authentication;

use App\Requests\Authentication\ForgotPasswordRequest;
use App\Services\AuthService;
use Inertia\Response;
use Tempest\Http\Responses\Redirect;
use Tempest\Http\Session\Session;
use Tempest\Router\Get;
use Tempest\Router\Post;

use function Tempest\Router\uri;

final readonly class ForgotPasswordController
{
    public function __construct(
        private AuthService $authService,
        private Session $session,
    ) {}

    #[Get('/forgot-password')]
    public function show(): Response
    {
        return inertia(
            component: 'Authentication/ForgotPassword',
            props: ['status' => $this->session->get('status')]
        );
    }

    #[Post('/forgot-password')]
    public function send(ForgotPasswordRequest $request): Redirect
    {
        $this->authService->sendPasswordResetEmail(
            email: $request->email,
        );

        $this->session->flash(
            key: 'status',
            value: 'If that email address is in our system, you will receive a password reset link shortly.'
        );

        return new Redirect(uri([self::class, 'show']));
    }
}