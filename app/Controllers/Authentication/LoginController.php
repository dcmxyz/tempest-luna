<?php

declare(strict_types=1);

namespace App\Controllers\Authentication;

use App\CommandBus\Authentication\LoginUser;
use App\Requests\Authentication\LoginRequest;
use Inertia\Response;
use Tempest\Http\Responses\Redirect;
use Tempest\Router\Get;
use Tempest\Router\Post;

use function Tempest\CommandBus\command;

final readonly class LoginController
{
    #[Get('/login')]
    public function create(): Response
    {
        return inertia(component: 'Authentication/Login');
    }

    #[Post('/login')]
    public function store(LoginRequest $request): Redirect
    {
        command(new LoginUser(
            email: $request->email,
            password: $request->password,
        ));

        return new Redirect('/');
    }
}
