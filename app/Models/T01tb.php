<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class T01tb extends Authenticatable
{
    protected $table = 't01tb';
    protected $casts = ['class' => 'string'];
    
    public $timestamps = false;
    public $primaryKey = "class";

    protected $fillable = array('class','name','type','style','post','process','traintype','board','kind','period','day','quota','extraquota','dayhour','publish','rank','quotatot','time1','time2','time3','time4','time5','time6','time7','holiday','classtype','trainday','trainhour','special','category','is_must_read','upload1','classified','elearnhr','classhr','cntflag','signin','chfchk','perchk','rkchk','subchfchk','subperchk','subrkchk','orgchk','yerly','times','planmk','english','remark','target','object','content','trace','profchk','branch','areachk','teaching','chkclass','branchname','commission','categoryone','precautions','samecourse','signupquota','signupexquota','branchcode','newstyle','registration','class_process','modifytime','modifyuser');

    function set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    function t51tb_sum_quota()
    {
        return $this->hasOne('App\Models\T51tb', 'class', 'class')->selectRaw('class, sum(quota) sum_quota')->groupBy('class');
    }

    function t51tb_assigned_terms()
    {
        return $this->hasMany('App\Models\T51tb', 'class', 'class')->selectRaw('class, LPAD(term, 2, 0) term')->groupBy(['class', DB::raw('LPAD(term, 2, 0)')]);
    }    

    // function t69tb_sum_checkcnt()
    // {
    //     return $this->hasOne('App\Models\T69tb', 'class', 'class')
    //                 ->selectRaw('class, sum(checkcnt) sum_checkcnt')
    //                 ->groupBy('class');
    // }

    function t69tbs()
    {
        return $this->hasMany('App\Models\T69tb', 'class', 'class');
    }



    function tmp_assign_sum_quota()
    {
        return $this->hasOne('App\Models\TmpAssignResult', 'class', 'class')->selectRaw('class, sum(quota) sum_quota')->groupBy('class');
    } 

    function t03tbs()
    {
        return $this->hasMany('App\Models\T03tb', 'class', 'class');
    }    
    
    function tmp_assigns()
    {
        return $this->hasMany('App\Models\TmpAssignResult', 'class', 'class');
    }

    function s01tb(){
        return $this->hasOne('App\Models\S01tb', 'code', 'type')->where('type', '=', 'K');
    }

    function online_updated_terms()
    {
        return $this->hasMany('App\Models\TmpAssignResult', 'class', 'class')
                    ->selectRaw('class, LPAD(term, 2, 0) term')
                    ->where('online_update', 1)
                    ->groupBy(['class', DB::raw('LPAD(term, 2, 0)')]);
    }

    function online_updated_quotas()
    {
        return $this->hasMany('App\Models\TmpAssignResult', 'class', 'class')
                    ->selectRaw('class, organ, sum(quota) sum_quota')
                    ->where('online_update', 1)
                    ->groupBy(['class', 'organ']);
    }

    function t51tb_organ_sum_quota()
    {
        return $this->hasMany('App\Models\T51tb', 'class', 'class')
                    ->selectRaw('class, organ, sum(quota) sum_quota')
                    ->groupBy(['class', 'organ']);        
    }

    function t02tbs()
    {
        return $this->hasMany('App\Models\T02tb', 'class', 'class')->with('min_grade_m17tb');
    }

    function t04tbs()
    {
        return $this->hasMany('App\Models\T04tb', 'class', 'class');
    }
    
    function s06tbs(){
        return $this->hasMany('App\Models\S06tb', 'yerly', 'yerly');
    }

    function s03tb()
    {
        return $this->hasOne('App\Models\S03tb', 'category', 'category');
    }

    // 委訓機關
    public function intrsutClassOrg()
    {
        return $this->belongsTo('App\Models\M17tb', 'commission', 'enrollorg');
    }    
}
  
