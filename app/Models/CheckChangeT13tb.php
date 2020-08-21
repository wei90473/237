<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CheckChangeT13tb extends Model
{
    protected $table = 'check_change_t13tbs';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

}