<?php
namespace App\Helpers;

use App\Helpers\Common;
use DateTime;
use App\Models\S02tb;

class SystemParam{
    public static function get()
    {
        $s02tb = S02tb::first();
        return $s02tb;
    }
}