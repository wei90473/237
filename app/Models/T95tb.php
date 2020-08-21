<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T95tb extends Model
{
    protected $table = 't95tb';

    public $timestamps = false;

    protected $fillable = array('class' ,'term' ,'times' ,'serno' ,'q11' ,'q12' ,'q13' ,'q14' ,'q15' ,'q21' ,'q22' ,'q23' ,'q31' ,'q32' ,'q33' ,'q41' ,'q42' ,'note' ,'upddate' ,'fillmk' ,'crtdate' ,'logdate' ,'loginorg');
}