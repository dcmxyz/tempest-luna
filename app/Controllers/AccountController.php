<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Middleware\MustBeAuthenticated;
use App\Models\User;
use App\Requests\Account\EmailRequest;
use App\Requests\Account\NameRequest;
use App\Requests\Account\PasswordRequest;
use App\Validation\UniqueEmailExcluding;
use Inertia\Response;
use Inertia\ResponseFactory;
use Tempest\Auth\Authentication\Authenticator;
use Tempest\Cryptography\Password\PasswordHasher;
use Tempest\Http\Request;
use Tempest\Http\Responses\Redirect;
use Tempest\Router\Get;
use Tempest\Router\Post;
use Tempest\Validation\Exceptions\ValidationFailed;
use Tempest\Validation\Validator;

use function App\Helpers\fail_validation;

final readonly class AccountController
{
    public function __construct(
        private Validator $validator,
        private Authenticator $authenticator,
        private PasswordHasher $passwordHasher,
    ) {}

    #[Get(uri: '/account', middleware: [MustBeAuthenticated::class])]
    public function index(): Response
    {
        return inertia(component: 'Account/Index');
    }

    #[Post(uri: '/account/name', middleware: [MustBeAuthenticated::class])]
    public function updateName(NameRequest $request): Response|ResponseFactory|Redirect
    {
        $this->authenticator->current()?->update(name: $request->get('name'));

        return new Redirect('/account');
    }

    /**
     * @throws ValidationFailed
     */
    #[Post(uri: '/account/email', middleware: [MustBeAuthenticated::class])]
    public function updateEmail(EmailRequest $request): Response|ResponseFactory|Redirect
    {
        $failures = $this->validator->validateValue(
            value: $request->get('email'),
            rules: new UniqueEmailExcluding(
                excludeId: $this->authenticator->current()->id,
            ),
        );

        if ($failures) {
            throw $this->validator->createValidationFailureException(['email' => $failures]);
        }

        $this->authenticator->current()?->update(email: $request->get('email'));

        return new Redirect('/account');
    }

    /**
     * @throws ValidationFailed
     */
    #[Post(uri: '/account/password', middleware: [MustBeAuthenticated::class])]
    public function updatePassword(PasswordRequest $request): Response|ResponseFactory|Redirect
    {
        $user = User::find(id: $this->authenticator->current()->id)
            ->include('password')
            ->first();

        if (! $this->passwordHasher->verify($request->get('current_password'), $user->password)) {
            fail_validation(
                field: 'current_password',
                message: 'The current password is incorrect.',
            );
        }

        $user->update(password: $this->passwordHasher->hash($request->get('password')));

        return new Redirect('/account');
    }

    /**
     * @throws ValidationFailed
     */
    #[Post(uri: '/account/delete', middleware: [MustBeAuthenticated::class])]
    public function deleteAccount(Request $request): Response|ResponseFactory|Redirect
    {
        $user = User::find(id: $this->authenticator->current()->id)
            ->include('password')
            ->first();

        if (! $this->passwordHasher->verify($request->get('current_password'), $user->password)) {
            fail_validation(
                field: 'current_password',
                message: 'The current password is incorrect.',
            );
        }

        $this->authenticator->current()?->delete();

        return new Redirect('/');
    }
}
