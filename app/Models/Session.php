<?php

namespace App\Models;

use Tempest\Database\PrimaryKey;
use Tempest\Database\Uuid;
use Tempest\DateTime\DateTime;

final class Session
{
    #[Uuid]
    public PrimaryKey $id;

    public string $data;

    public DateTime $created_at;

    public DateTime $last_active_at;

    public ?string $user_id = null;

    public ?string $ip_address = null;

    public ?string $user_agent = null;
}
