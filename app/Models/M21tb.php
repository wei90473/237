<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class M21tb extends Model
{
    protected $table = 'm21tb';

    public $timestamps = false;

    protected $fillable = array('enrollorg', 'userid', 'username', 'section', 'telnoa', 'telnob', 'telnoc', 'email', 'keyman', 'selfid', 'userpsw', 'status', 'pswerrcnt', 'crtdate', 'crtuserid', 'account');
}