<?php

declare(strict_types=1);

namespace App\Models\Traits;

use Tempest\DateTime\DateTime;

trait HasTimestamps
{
    public ?DateTime $created_at = null;
    public ?DateTime $updated_at = null;
}
