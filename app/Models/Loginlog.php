<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loginlog extends Model
{
    use SoftDeletes;

    protected $table = 'loginlog';

    protected $primaryKey ='loginlog_id';

    protected $dates = ['deleted_at'];

    protected $fillable = array('account', 'ip');

}
