<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T73tb extends Model
{
    protected $table = 't73tb';

    public $timestamps = false;

    protected $fillable = array('year', 'times', 'serno', 'q1', 'q2', 'q3', 'q4', 'q5', 'q6', 'q7', 'q8', 'q9', 'q10', 'dept', 'extdept', 'site1', 'site2', 'site3', 'site4', 'applycnt', 'apply', 'extapply', 'duty', 'comment');
}