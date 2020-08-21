<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T23tb extends Model
{
    protected $table = 't23tb';

     public $timestamps = false;

    protected $fillable = array('type' ,'class' ,'term' ,'date' ,'lovecnt','sincnt' ,'donecnt' ,'dtwocnt' ,'meacnt' ,'meavegan' ,'luncnt' ,'lunvegan' ,'dincnt' ,'dinvegan' ,'tabtype' ,'tabcnt' ,'tabvegan' ,'tabunit' ,'buftype' ,'bufcnt' ,'bufvegan' ,'bufunit' ,'teacnt' ,'teaunit' ,'teatime' ,'request' ,'affirm' ,'sinunit' ,'doneunit' ,'dtwounit' ,'meaunit' ,'lununit' ,'dinunit' ,'otheramt' ,'siteamt' ,'upddate');
}