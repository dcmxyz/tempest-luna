<?php

declare(strict_types=1);

namespace App\Session;

use App\Models\Session as AppSession;
use Tempest\Auth\Authentication\SessionAuthenticator;
use Tempest\Clock\Clock;
use Tempest\Core\AppConfig;
use Tempest\Cryptography\Encryption\Encrypter;
use Tempest\DateTime\FormatPattern;
use Tempest\Http\Cookie\CookieManager;
use Tempest\Http\Request;
use Tempest\Http\Session\Session;
use Tempest\Http\Session\SessionConfig;
use Tempest\Http\Session\SessionCreated;
use Tempest\Http\Session\SessionDeleted;
use Tempest\Http\Session\SessionId;
use Tempest\Http\Session\SessionIdResolver;
use Tempest\Http\Session\SessionManager;

use function Tempest\Database\query;
use function Tempest\EventBus\event;
use function Tempest\Support\str;

/**
 * Database-backed session manager with encrypted payloads, replacing Tempest's default.
 * Tracks user_id, ip_address, and user_agent for auditing and session management.
 */
final readonly class AppSessionManager implements SessionManager
{
    public function __construct(
        private AppConfig $appConfig,
        private Clock $clock,
        private SessionConfig $config,
        private Request $request,
        private Encrypter $encrypter,
        private CookieManager $cookieManager,
        private SessionIdResolver $sessionIdResolver,
    ) {}

    public function getOrCreate(SessionId $id): Session
    {
        $now = $this->clock->now();
        $session = $this->load($id);

        if (! $session instanceof Session) {
            $session = new Session(
                id: $id,
                createdAt: $now,
                lastActiveAt: $now,
            );

            event(new SessionCreated($session));
        }

        return $session;
    }

    public function save(Session $session): void
    {
        $session->lastActiveAt = $this->clock->now();

        $userId = $session->get(SessionAuthenticator::AUTHENTICATABLE_KEY);

        $existing = query(AppSession::class)
            ->select()
            ->where('id', (string) $session->id)
            ->first();

        if ($existing === null) {
            query(AppSession::class)
                ->insert(
                    id: (string) $session->id,
                    data: $this->serializeData($session->data),
                    created_at: $session->createdAt,
                    last_active_at: $session->lastActiveAt,
                    user_id: $userId ? (string) $userId : null,
                    ip_address: $this->resolveIpAddress(),
                    user_agent: $this->request->headers->get('user-agent'),
                )
                ->execute();

            return;
        }

        query(AppSession::class)
            ->update(
                data: $this->serializeData($session->data),
                last_active_at: $session->lastActiveAt,
                user_id: $userId ? (string) $userId : null,
            )
            ->where('id', (string) $session->id)
            ->execute();
    }

    public function delete(Session $session): void
    {
        query(AppSession::class)
            ->delete()
            ->where('id', (string) $session->id)
            ->execute();

        event(new SessionDeleted($session->id));
    }

    public function isValid(Session $session): bool
    {
        return $this->clock->now()->before(
            other: $session->lastActiveAt->plus($this->config->expiration),
        );
    }

    public function deleteExpiredSessions(): void
    {
        $expired = $this->clock
            ->now()
            ->minus($this->config->expiration);

        $expiredSessions = query(AppSession::class)
            ->select()
            ->where('last_active_at < ?', $expired->format(FormatPattern::SQL_DATE_TIME))
            ->all();

        foreach ($expiredSessions as $expiredSession) {
            query(AppSession::class)
                ->delete()
                ->where('id', $expiredSession->id)
                ->execute();

            event(new SessionDeleted(new SessionId((string) $expiredSession->id)));
        }
    }

    private function load(SessionId $id): ?Session
    {
        $session = query(AppSession::class)
            ->select()
            ->where('id', (string) $id)
            ->first();

        if ($session === null) {
            return null;
        }

        return new Session(
            id: $id,
            createdAt: $session->created_at,
            lastActiveAt: $session->last_active_at,
            data: $this->unserializeData($session->data),
        );
    }

    private function serializeData(array $data): string
    {
        return (string) $this->encrypter->encrypt(serialize($data));
    }

    private function unserializeData(string $data): array
    {
        return unserialize($this->encrypter->decrypt($data), [
            'allowed_classes' => true,
        ]);
    }

    private function resolveIpAddress(): ?string
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
        }

        return $_SERVER['REMOTE_ADDR'] ?? null;
    }

    private function sessionCookieName(): string
    {
        return str($this->appConfig->name ?? 'tempest')
            ->snake()
            ->append('_session_id')
            ->toString();
    }

    public function destroySession(): void
    {
        $session = $this->load($this->sessionIdResolver->resolve());

        if ($session !== null) {
            $this->delete($session);
        }

        $this->cookieManager->remove($this->sessionCookieName());
    }
}
