<?php

use Inertia\Configs\InertiaConfig;
use Inertia\Configs\ValidationConfig;

return new InertiaConfig(
    validation: new ValidationConfig(
        localize_fields: false,
    ),
);
