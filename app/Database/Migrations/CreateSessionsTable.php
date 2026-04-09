<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use Tempest\Database\MigratesUp;
use Tempest\Database\QueryStatement;
use Tempest\Database\QueryStatements\CreateTableStatement;

final class CreateSessionsTable implements MigratesUp
{
    private(set) string $name = '0001-01-01_create_sessions_table';

    public function up(): QueryStatement
    {
        return new CreateTableStatement('sessions')
            ->primary(uuid: true)
            ->datetime('created_at')
            ->datetime('last_active_at')
            ->text('data')
            ->raw('user_id UUID NULL') // Use Postgres native UUID type
            ->string('ip_address', length: 45, nullable: true)
            ->text('user_agent', nullable: true)
            ->index('user_id')
            ->index('last_active_at');
    }
}
