<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\HasTimestamps;
use Tempest\Auth\Authentication\Authenticatable;
use Tempest\Database\IsDatabaseModel;
use Tempest\Database\PrimaryKey;
use Tempest\Database\Uuid;
use Tempest\DateTime\DateTime;
use Tempest\Mapper\Hidden;

final class User implements Authenticatable
{
    use IsDatabaseModel;
    use HasTimestamps;

    #[Uuid]
    public PrimaryKey $id;

    public function __construct(
        public string $name,

        public string $email,

        #[\SensitiveParameter]
        #[Hidden]
        public ?string $password = null,

        #[\SensitiveParameter]
        #[Hidden]
        public ?string $two_factor_secret = null,

        #[\SensitiveParameter]
        #[Hidden]
        public ?string $two_factor_recovery_codes = null,

        public ?DateTime $two_factor_confirmed_at = null,

        public ?DateTime $email_verified_at = null,
    ) {}
}
