<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Elearn_history extends Model
{
    protected $table = 'elearning_history';

    protected $primaryKey = 'id';

    protected $fillable = array('elearn_class_id', 'idno', 'status', 'created_at', 'updated_at');

}