<?php

use Illuminate\Support\Facades\Auth;

return [
    'avatar_column' => 'avatar_url',
    'disk' => 'public',
    'visibility' => 'public', // or replace by filesystem disk visibility with fallback value
];
