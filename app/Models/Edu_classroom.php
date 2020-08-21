<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Edu_classroom extends Model
{
    protected $table = 'edu_classroom';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = array('id','roomno', 'roomname', 'fullname', 'roomcla','zonecode', 'num', 'managerno', 'summary');

    public function location()
    {
        return $this->hasOne('App\Models\Edu_classcode', 'code', 'roomcla')->where('class', '=', 49);
    }

    public function classroomcls()
    {
        return $this->hasOne('App\Models\Edu_classroomcls', 'croomclsno', 'zonecode');
    }    

    // edu_classroomcls
}