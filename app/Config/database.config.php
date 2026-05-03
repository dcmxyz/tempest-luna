<?php

declare(strict_types=1);

use Tempest\Database\Config\MysqlConfig;
use Tempest\Database\Config\PostgresConfig;
use Tempest\Database\Config\SQLiteConfig;

use function Tempest\env;
use function Tempest\root_path;

$pgsqlConfig = new PostgresConfig(
    host: env('DATABASE_HOST', default: '127.0.0.1'),
    port: env('DATABASE_PORT', default: '5432'),
    username: env('DATABASE_USERNAME', default: 'postgres'),
    password: env('DATABASE_PASSWORD', default: ''),
    database: env('DATABASE_DATABASE', default: 'luna'),
);

$mysqlConfig = new MysqlConfig(
    host: env('DATABASE_HOST', default: '127.0.0.1'),
    port: env('DATABASE_PORT', default: '3306'),
    username: env('DATABASE_USERNAME', default: 'root'),
    password: env('DATABASE_PASSWORD', default: ''),
    database: env('DATABASE_DATABASE', default: 'luna'),
);

$sqliteConfig = new SQLiteConfig(
    path: root_path(env('DATABASE_PATH', default: '.tempest/database.sqlite')),
    persistent: env('DATABASE_PERSISTENT', default: true),
);

return match (env('DATABASE_CONFIG', default: 'sqlite')) {
    'sqlite' => $sqliteConfig,
    'pgsql' => $pgsqlConfig,
    'mysql' => $mysqlConfig,
    default => throw new InvalidArgumentException('Only the "sqlite", "pgsql" and "mysql" configs are supported.')
};
