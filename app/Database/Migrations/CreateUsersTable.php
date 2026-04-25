<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use Tempest\Database\MigratesUp;
use Tempest\Database\QueryStatement;
use Tempest\Database\QueryStatements\CreateTableStatement;

final class CreateUsersTable implements MigratesUp
{
    public string $name = '0002-02-02_create_users_table';

    public function up(): QueryStatement
    {
        return new CreateTableStatement('users')
            ->primary(uuid: true)
            ->string('name')
            ->string('email')
            ->string('password', nullable: true)
            ->string('remember_token', length: 130, nullable: true)
            ->datetime('email_verified_at', nullable: true)
            ->text('two_factor_secret', nullable: true)
            ->text('two_factor_recovery_codes', nullable: true)
            ->datetime('two_factor_confirmed_at', nullable: true)
            ->datetime('created_at', nullable: true)
            ->datetime('updated_at', nullable: true)
            ->unique('email')
            ->index('remember_token');
    }
}
