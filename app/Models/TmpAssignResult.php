<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TmpAssignResult extends Model
{
    protected $table = 'tmp_assign_result';
    protected $casts = ['class' => 'string'];
    public $timestamps = false;
    protected $fillable = array('id' ,'class' ,'term' ,'organ' ,'quota', 'online_update', 'created_at', 'updated_at');
}