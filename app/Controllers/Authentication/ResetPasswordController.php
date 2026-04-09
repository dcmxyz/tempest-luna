<?php

declare(strict_types=1);

namespace App\Controllers\Authentication;

use App\CommandBus\Authentication\ResetPassword;
use App\Requests\Authentication\ResetPasswordRequest;
use Inertia\Response;
use Tempest\Http\Responses\Redirect;
use Tempest\Router\Get;
use Tempest\Router\Post;

use function Tempest\CommandBus\command;
use function Tempest\Router\uri;

final readonly class ResetPasswordController
{
    #[Get('/reset-password/{token}')]
    public function show(string $token): Response
    {
        return inertia(component: 'Authentication/ResetPassword', props: ['token' => $token]);
    }

    #[Post('/reset-password')]
    public function store(ResetPasswordRequest $request): Redirect
    {
        command(new ResetPassword(
            token: $request->token,
            password: $request->password,
        ));

        return new Redirect(uri([LoginController::class, 'create']));
    }
}