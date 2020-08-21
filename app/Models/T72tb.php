<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T72tb extends Model
{
    protected $table = 't72tb';

    public $timestamps = false;

    protected $fillable = array('class' ,'term' ,'times' ,'serno' ,'q11' ,'q12' ,'q13' ,'q21' ,'q22' ,'q41' ,'q42' ,'foocolor' ,'fooqty' ,'footast' ,'fooenvir' ,'fooservice' ,'fooother' ,'q43' ,'boaclear' ,'boaservice' ,'boaeqip' ,'boaother' ,'whorate' ,'reason' ,'sex' ,'age' ,'ecode' ,'dept' ,'extdept' ,'rank' ,'extrank' ,'duty' ,'dutytime' ,'officertime' ,'wkyear' ,'wkmonth' ,'q1note' ,'addcourse' ,'delcourse' ,'q2note' ,'q3note' ,'addprof' ,'q4note' ,'q5note');
}