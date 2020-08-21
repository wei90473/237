<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T13tb extends Model
{
    protected $table = 't13tb';

    public $timestamps = false;

    // protected $fillable = array('class', 'term', 'idno', 'organ' ,'lname' ,'sname' ,'type','rank' ,'division' ,'sponsor1' ,'telnoa1' ,'telnob1' ,'telnoc1' ,'faxnoa1' ,'faxnob1' ,'sponsor2' ,'telnoa2' ,'telnob2' ,'telnoc2' ,'faxnoa2' ,'faxnob2' ,'zip' ,'address' ,'userpsw' ,'status' ,'kind' ,'email' ,'effdate' ,'expdate', 'offname', 'offemail', 'offtel', 'not_present_notification', 'dept', 'ecode', 'education');
    protected $guarded = [];

    public function t27tb(){
        return $this->hasOne('App\Models\T27tb', 'class', 'class')->where('term', '=', $this->term);
    }

    public function m02tb(){
        return $this->belongsTo('App\Models\M02tb', 'idno', 'idno');
    }

    public function m13tb(){
        return $this->belongsTo('App\Models\M13tb', 'organ', 'organ');
    }

    public function t04tb(){
        return $this->belongsTo('App\Models\T04tb', 'class', 'class')->where('term', '=', $this->term);
    }
}