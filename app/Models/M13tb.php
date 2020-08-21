<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class M13tb extends Model
{
    protected $table = 'm13tb';

    public $timestamps = false;

    protected $fillable = array('organ' ,'lname' ,'sname' ,'type' ,'rank' ,'division' ,'sponsor1' ,'telnoa1' ,'telnob1' ,'telnoc1' ,'faxnoa1' ,'faxnob1' ,'sponsor2' ,'telnoa2' ,'telnob2' ,'telnoc2' ,'faxnoa2' ,'faxnob2' ,'zip' ,'address' ,'userpsw' ,'status' ,'kind' ,'email' ,'effdate' ,'expdate');

}