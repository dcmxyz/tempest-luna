<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use Tempest\Database\Config\DatabaseConfig;
use Tempest\Database\Config\PostgresConfig;
use Tempest\Database\MigratesUp;
use Tempest\Database\QueryStatement;
use Tempest\Database\QueryStatements\CreateTableStatement;

final class CreateSessionsTable implements MigratesUp
{
    private(set) string $name = '0001-01-01_create_sessions_table';

    public function __construct(
        private readonly DatabaseConfig $databaseConfig,
    ) {}

    public function up(): QueryStatement
    {
        $createTableStatement = new CreateTableStatement('sessions')
            ->primary(uuid: true)
            ->datetime('created_at')
            ->datetime('last_active_at')
            ->text('data');

        if ($this->databaseConfig instanceof PostgresConfig) {
            $createTableStatement->raw(statement: "user_id UUID NULL");
        } else {
            $createTableStatement->string(name: 'user_id', length: 36, nullable: true);
        }

        $createTableStatement
            ->string('ip_address', length: 45, nullable: true)
            ->text('user_agent', nullable: true)
            ->index('user_id')
            ->index('last_active_at');

        return $createTableStatement;
    }
}
