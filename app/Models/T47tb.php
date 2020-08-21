<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T47tb extends Model
{
    protected $table = 't47tb';

     public $timestamps = false;

    protected $fillable = array('class' ,'term' ,'degree' ,'summary' ,'enroll' ,'validdate' ,'county' ,'site' ,'sdate' ,'edate' ,'credit' ,'unit' ,'restriction' ,'lodging' ,'meal' ,'upload2' ,'grade' ,'leave' ,'file1' ,'file2' ,'file3' ,'file4' ,'file5' ,'remark');
}