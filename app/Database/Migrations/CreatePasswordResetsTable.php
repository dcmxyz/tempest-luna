<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use Tempest\Database\MigratesUp;
use Tempest\Database\QueryStatement;
use Tempest\Database\QueryStatements\CreateTableStatement;

final class CreatePasswordResetsTable implements MigratesUp
{
    public string $name = '0003-03-03_create_password_resets_table';

    public function up(): QueryStatement
    {
        return new CreateTableStatement('password_resets')
            ->primary(uuid: true)
            ->string('email')
            ->string('token')
            ->datetime('created_at', nullable: true)
            ->unique('email');
    }
}
