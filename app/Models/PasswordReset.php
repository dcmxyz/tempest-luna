<?php

declare(strict_types=1);

namespace App\Models;

use Tempest\Auth\Authentication\Authenticatable;
use Tempest\Database\IsDatabaseModel;
use Tempest\Database\PrimaryKey;
use Tempest\Database\Uuid;
use Tempest\DateTime\DateTime;
use Tempest\Mapper\Hidden;

final class PasswordReset implements Authenticatable
{
    use IsDatabaseModel;

    #[Uuid]
    public PrimaryKey $id;

    public function __construct(
        public string $email,

        #[\SensitiveParameter]
        #[Hidden]
        public string $token,

        public ?DateTime $created_at = null,
    ) {}
}