<?php

declare(strict_types=1);

namespace App\Controllers\Authentication;

use App\Requests\Authentication\LoginRequest;
use App\Services\AuthService;
use Inertia\Response;
use Inertia\ResponseFactory;
use Tempest\Http\Responses\Redirect;
use Tempest\Router\Get;
use Tempest\Router\Post;

final readonly class LoginController
{
    public function __construct(
        private AuthService $authService,
    ) {}

    #[Get('/login')]
    public function show(): Response
    {
        return inertia(component: 'Authentication/Login');
    }

    #[Post('/login')]
    public function store(LoginRequest $request): Response|ResponseFactory|Redirect
    {
        $loggedIn = $this->authService->login(
            email: $request->email,
            password: $request->password,
            remember: true,
        );

        if (! $loggedIn) {
            return inertia('Authentication/Login', [
                'errors' => ['email' => 'These credentials do not match our records.'],
            ]);
        }

        return new Redirect('/');
    }
}
