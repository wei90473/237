<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T91tb extends Model
{
    protected $table = 't91tb';

    public $timestamps = false;

    protected $fillable = array('class' ,'term' ,'times' ,'serno' ,'q1' ,'q2' ,'q3' ,'q4' ,'q5' ,'q6' ,'q7' ,'sex' ,'age' ,'ecode' ,'dept' ,'extdept' ,'rank' ,'extrank' ,'duty' ,'dutytime' ,'officertime' ,'wkyear' ,'wkmonth' ,'note');
}