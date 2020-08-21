<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Check_change_t13tb extends Model
{
    protected $table = 'check_change_t13tbs';

    protected $primaryKey = 'id';

    protected $fillable = array(
        'class',
        'term',
        'idno',
        'no',
        'groupno',
        'organ',
        'dept',
        'rank',
        'position',
        'ecode',
        'education',
        'offname',
        'offemail',
        'offtel',
        'dorm',
        'vegan',
        'nonlocal',
        'extradorm',
        'status'
    );

}