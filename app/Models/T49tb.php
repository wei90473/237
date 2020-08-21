<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class T49tb extends Authenticatable
{
    protected $table = 't49tb';

    public $timestamps = false;

    protected $guarded = [];
}