<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    function setting(string $key): mixed
    {
        return Setting::get($key);
    }
}
