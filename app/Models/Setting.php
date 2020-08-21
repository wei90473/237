<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{
    use SoftDeletes;

    protected $table = 'setting';

    protected $primaryKey ='setting_id';

    protected $dates = ['deleted_at'];

    protected $fillable = array('unit', 'value', 'comment', 'explanation', 'type');

}
