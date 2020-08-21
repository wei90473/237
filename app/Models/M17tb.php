<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class M17tb extends Model
{
    protected $table = 'm17tb';
    public $primaryKey = "enrollorg";
    public $timestamps = false;
    protected $casts = ['enrollorg' => 'string'];
    protected $fillable = array('enrollorg' ,'enrollname' ,'organ' ,'division' ,'sponsor' ,'telnoa' ,'telnob' ,'telnoc' ,'email' ,'address' ,'zip' ,'grade' ,'uporgan' ,'enrollpsw' ,'status' ,'pswerrcnt' ,'idno' ,'crossarea');

    function m17tb_by_grade()
    {
        return $this->hasOne('App\Models\M17tb', 'organ', 'organ')->where('grade', '=', $this->min_grade);
    }

    function t51tb()
    {
        return $this->hasOne('App\Models\T51tb', 'organ', 'enrollorg')->where('class', $this->class);
    }

    function t69tb()
    {
        return $this->hasOne('App\Models\T69tb', 'organ', 'enrollorg')->where('class', $this->class);
    }

    function setClass($class)
    {
        $this->attributes['class'] = $class;
    }

}