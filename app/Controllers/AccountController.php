<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Middleware\MustBeAuthenticated;
use App\Models\Session;
use App\Models\User;
use App\Requests\Account\EmailRequest;
use App\Requests\Account\NameRequest;
use App\Requests\Account\PasswordRequest;
use App\Services\AuthService;
use App\Validation\UniqueEmailExcluding;
use Inertia\Response;
use Inertia\ResponseFactory;
use JsonException;
use Tempest\Auth\Authentication\Authenticator;
use Tempest\Cryptography\Password\PasswordHasher;
use Tempest\Http\ContentType;
use Tempest\Http\Request;
use Tempest\Http\Responses\Ok;
use Tempest\Http\Responses\Redirect;
use Tempest\Http\Session\Session as HttpSession;
use Tempest\Router\Get;
use Tempest\Router\Post;
use Tempest\Validation\Exceptions\ValidationFailed;
use Tempest\Validation\Validator;

use function App\Helpers\fail_validation;
use function App\Helpers\parse_user_agent;
use function Tempest\Support\arr;

final readonly class AccountController
{
    public function __construct(
        private Validator $validator,
        private Authenticator $authenticator,
        private PasswordHasher $passwordHasher,
        private HttpSession $session,
        private AuthService $authService,
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
        $user = $this->authenticator->current();

        $failures = $this->validator->validateValue(
            value: $request->get('email'),
            rules: new UniqueEmailExcluding(
                excludeId: $user->id,
            ),
        );

        if ($failures) {
            throw $this->validator->createValidationFailureException(['email' => $failures]);
        }

        $user?->update(
            email: $request->get('email'),
            email_verified_at: null,
        );

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

    /**
     * @throws JsonException
     */
    #[Get(uri: '/account/sessions', middleware: [MustBeAuthenticated::class])]
    public function sessions(): \Tempest\Http\Response
    {
        $user = $this->authenticator->current();
        $user?->load('sessions');

        $sessions = arr($user?->sessions)
            ->sortByCallback(static fn(Session $session) => $session->last_active_at->getTimestamp())
            ->slice(0, 6)
            ->map(function (Session $session) {
                return [
                    'id' => (string) $session->id,
                    'ip_address' => $session->ip_address,
                    'user_agent' => parse_user_agent($session->user_agent),
                    'last_active_at' => $session->last_active_at->format('Y-MM-dd HH:mm:ss'),
                    'is_current_device' => (string) $session->id === (string) $this->session->id,
                ];
            });

        return new Ok(json_encode($sessions, JSON_THROW_ON_ERROR))
            ->setContentType(ContentType::JSON);
    }

    #[Post(uri: '/account/sessions/logout', middleware: [MustBeAuthenticated::class])]
    public function logoutAllSessions(): \Tempest\Http\Response
    {
        $user = $this->authenticator->current();
        $user?->load('sessions');

        // Remove out all other sessions except the current one
        foreach ($user?->sessions as $session) {
            if ((string) $session->id !== (string) $this->session->id) {
                $session->delete();
            }
        }

        // Rotating the user's remember token invalidates any "remember me" cookies still held by other devices.
        // Note: this also invalidates the current device's remember cookie, but since the current session
        // is still active, the current user will not be logged out.
        $this->authService->rotateRememberToken($user);

        return new Redirect('/account');
    }
}
