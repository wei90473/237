<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApplyModifyLogForAmdin extends Model
{
    protected $table = 'apply_modify_logs_for_admin';

    protected $primaryKey = 'id';

    protected $fillable = array('class', 'term', 'modify_type', 'idno', 'new_idno', 'new_term', 'modify_user_id', 'student_dept', 'new_student_dept', 'created_at', 'updated_at');
    
    public function m02tb()
    {
        return $this->belongsTo('App\Models\M02tb', 'idno', 'idno');
    }

    public function new_m02tb()
    {
        return $this->belongsTo('App\Models\M02tb', 'new_idno', 'idno');
    } 
    
    public function modify_user()
    {
        return $this->belongsTo('App\Models\M09tb', 'modify_user_id', 'id');
    }      
}