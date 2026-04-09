<?php

declare(strict_types=1);

namespace App\Controllers\Authentication;

use App\Config\Definitions\AuthConfig;
use App\Requests\Authentication\RegisterRequest;
use App\Services\AuthService;
use Inertia\Response;
use Tempest\Auth\Authentication\Authenticator;
use Tempest\Http\Responses\Redirect;
use Tempest\Router\Get;
use Tempest\Router\Post;

final readonly class RegisterController
{
    public function __construct(
        private Authenticator $authenticator,
        private AuthConfig $authConfig,
        private AuthService $authService,
    ) {}

    #[Get('/register')]
    public function show(): Response
    {
        return inertia(component: 'Authentication/Register');
    }

    #[Post('/register')]
    public function store(RegisterRequest $request): Response|Redirect
    {
        $user = $this->authService->register(
            name: $request->name,
            email: $request->email,
            password: $request->password,
        );

        if ($this->authConfig->loginAfterRegister) {
            $this->authenticator->authenticate($user);
        }

        return new Redirect('/');
    }
}
