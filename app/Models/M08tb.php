<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class M08tb extends Model
{
    protected $table = 'm08tb';

    public $timestamps = false;

    protected $fillable = array('idno');
}