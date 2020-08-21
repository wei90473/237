<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Apply_modify_log extends Model
{
    protected $table = 'apply_modify_logs';

    protected $primaryKey = 'id';

    protected $fillable = array('type', 'class', 'term', 'new_term', 'idno', 'new_idno', 'status', 'created_at', 'updated_at');

}