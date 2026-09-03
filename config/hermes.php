<?php

return [
    'shared_secret' => env('HERMES_SHARED_SECRET'),

    'allowed_actors' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('HERMES_ALLOWED_ACTORS', ''))
    ))),
];
