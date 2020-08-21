<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T55tb extends Model
{
    protected $table = 't55tb';

    public $timestamps = false;

    protected $fillable = array('class' ,'term' ,'times' ,'serno' ,'q11' ,'q12' ,'q13' ,'q14' ,'q21' ,'q22' ,'q23' ,'q31' ,'q32' ,'q33' ,'q53' ,'q54' ,'sex' ,'age' ,'ecode' ,'dept' ,'rank' ,'duty' ,'dutytime' ,'officertime' ,'reason' ,'q15' ,'q24' ,'q34' ,'q42' ,'q51' ,'q52' ,'q55' ,'qfood' ,'qboard');
}