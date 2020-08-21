<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T75tb extends Model
{
    protected $table = 't75tb';

    public $timestamps = false;

    protected $fillable = array('class' ,'term' ,'times' ,'serno' ,'q11' ,'q12' ,'q13' ,'q14' ,'q15' ,'q21' ,'q22' ,'q23' ,'q41' ,'q42' ,'sex' ,'age' ,'ecode' ,'dept' ,'extdept' ,'rank' ,'extrank' ,'duty' ,'dutytime' ,'officertime' ,'wkyear' ,'wkmonth' ,'note');
}