<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplyModifyLog extends Model
{
    protected $table = 'apply_modify_logs';
    public $guarded = [];

    public function newM02tb()
    {
        return $this->belongsTo('App\m02tb', 'new_idno', 'idno');
    }

    public function m02tb()
    {
        return $this->belongsTo('App\m02tb', 'idno', 'idno');
    }    
}
