<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentGrade extends Model
{
    protected $table = 'student_grades';

    protected $fillable = array('id', 'sub_option_id', 'idno', 'grade', 'created_at', 'updated_at');

}