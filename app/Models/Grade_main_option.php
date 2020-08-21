<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grade_main_option extends Model
{
    protected $table = 'grade_main_option';

    protected $primaryKey = 'id';

    protected $fillable = array('class', 'term', 'name', 'persent', 'created_at', 'updated_at');

    public function grade_sub_options()
    {
        return $this->hasMany('App\Models\Grade_sub_option', 'main_option_id', 'id')->orderBy('id');
    }

    public function grade_sub_options_with_grade()
    {
        return $this->hasMany('App\Models\Grade_sub_option', 'main_option_id', 'id')->with(['student_grades']);
    }

    public function t04tb(){
        return $this->belongsTo('App\Models\T04tb', 'class', 'class')->where('term', '=', $this->term);
    }

}