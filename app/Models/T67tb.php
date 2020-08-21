<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class T67tb extends Authenticatable
{
    protected $table = 't67tb';

    public $timestamps = false;

    protected $guarded = [];
}