<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class M16tb extends Model
{
    protected $table = 'm16tb';

    public $timestamps = false;

    protected $fillable = array('idno', 'no', 'specialty');
}