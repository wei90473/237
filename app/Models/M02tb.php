<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class M02tb extends Model
{
    protected $table = 'm02tb';
    
    public $incrementing = false;
    protected $primaryKey = 'idno';

    protected $fillable = array('idno' ,'lname' ,'fname' ,'cname' ,'ename' ,'sex' ,'birth' ,'organ' ,'dept' ,'position' ,'education' ,'offtela1' ,'offtelb1' ,'offtelc1' ,'offtela2' ,'offtelb2' ,'offtelc2' ,'offfaxa' ,'offfaxb' ,'homtela' ,'homtelb' ,'mobiltel' ,'email' ,'dgpatel' ,'offemail' ,'offzip' ,'homzip' ,'offaddr1' ,'offaddr2' ,'homaddr1' ,'homaddr2' ,'send' ,'chief' ,'personnel' ,'aborigine' ,'handicap' ,'datadate' ,'enrollid' ,'rank' ,'ecode' ,'chfcod' ,'popesn' ,'rcod1b' ,'rcod1e' ,'rcod2b' ,'rcod2e', 'identity', 'special_situation','is_student', 'is_teacher' ,'is_worker' ,'is_colleague',);
    
    public function m21tb()
    {
        return $this->hasOne('App\Models\M21tb', 'userid', 'idno')->orderBy('crtdate', 'desc');
    }

    public function m22tb()
    {
        return $this->hasOne('App\Models\M22tb', 'userid', 'idno')->orderBy('crtdate', 'desc');
    }
 
    public function student_account()
    {
        return $this->hasOne('App\Models\M22tb', 'userid', 'idno')->orderBy('crtdate', 'desc');
    }

    public function m17tb()
    {
        return $this->belongsTo('App\Models\M17tb', 'enrollid', 'enrollorg');
    }

    public function m13tb()
    {
        return $this->belongsTo('App\Models\M13tb', 'organ', 'organ');
    }    
}