<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grade_sub_option extends Model
{
    protected $table = 'grade_sub_option';

    protected $primaryKey = 'id';

    protected $fillable = array('main_option_id', 'name', 'persent', 'created_at', 'updated_at');

    public function student_grades()
    {
        return $this->hasMany('App\Models\StudentGrade', 'sub_option_id', 'id');
    }    
}