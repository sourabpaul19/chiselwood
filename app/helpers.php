<?php 

use App\Models\Setting;

function setting($key, $default = null)
{
    return Setting::get($key, $default);
}
