<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class T04tb extends Authenticatable
{
    protected $table = 't04tb';
    protected $casts = ['class' => 'string'];
    public $timestamps = false;

    protected $fillable = array('class' ,'term' ,'sdate' ,'edate' ,'site' ,'quota' ,'sponsor' ,'section' ,'signon' ,'schratio' ,'actratio' ,'basescr' ,'schitem1' ,'schrate1' ,'schitem2' ,'schrate2' ,'schitem3' ,'schrate3' ,'schitem4' ,'schrate4' ,'schitem5' ,'schrate5' ,'reqcnt' ,'fincnt' ,'kind' ,'client' ,'invoice' ,'time' ,'remark' ,'publish1' ,'publish2' ,'publish3' ,'notice' ,'force2' ,'lineup' ,'fee' ,'diploma' ,'pubsdate' ,'pubedate' ,'pubdatemk' ,'regcnt' ,'passcnt' ,'endcnt' ,'snsmk', 'counselor', 'site_branch', 'apply_code', 'apply_password', 'apply_limit', 'assign_type', 'officially_enroll', 'secondary_enroll', 'process_complete', 'lock', 'staystartdate', 'stayenddate', 'staystarttime', 'stayendtime', 'location');

    public function terms()
    {
        return $this->hasMany('App\Models\T04tb', 'class', 'class')
                    ->select("term")
                    ->where('term', '<>', $this->term)
                    ->orderBy('term');
    }

    public function t01tb()
    {
        return $this->belongsTo('App\Models\T01tb', 'class', 'class')->with(['s01tb']);
    }

    public function getSdateformatAttribute()
    {
        $this->sdate = (string)$this->sdate;
        if (strlen($this->sdate) !== 7) return false;

        $year = substr($this->sdate, 0, 3);
        $month = substr($this->sdate, 3, 2);
        $day = substr($this->sdate, 5, 2);

        return "{$year}/{$month}/{$day}";
    }

    public function getEdateformatAttribute()
    {
        $this->edate = (string)$this->edate;
        if (strlen($this->edate) !== 7) return false;

        $year = substr($this->edate, 0, 3);
        $month = substr($this->edate, 3, 2);
        $day = substr($this->edate, 5, 2);

        return "{$year}/{$month}/{$day}";
    }

    public function m09tb()
    {
        return $this->hasOne('App\Models\M09tb', 'userid', 'sponsor');
    }

    public function t03tb_sum_quota()
    {
        return $this->hasOne('App\Models\T03tb', 'class', 'class')
                    ->where('term', '=', $this->term)
                    ->selectRaw("class, term, sum(quota) sum_quota")
                    ->groupBy(["class", "term"]);
    }

    public function t03tb()
    {
        return $this->hasMany('App\Models\T03tb', 'class', 'class')
                    ->where('term', '=', $this->term);
    }

    public function t36tbs()
    {
        return $this->hasMany('App\Models\T36tb', 'class', 'class')
                    ->where('term', '=', $this->term);
    }

    public function t06tbs()
    {
        return $this->hasMany('App\Models\T06tb', 'class', 'class')
                    ->where('term', '=', $this->term)
                    ->orderBy('course', 'asc');
    }

    public function t05tbs()
    {
        return $this->hasMany('App\Models\T05tb', 'class', 'class')->where('term', '=', $this->term);
    }

    public function online_apply_organs()
    {
        return $this->hasMany('App\Models\OnlineApplyOrgan', 'class', 'class')->where('term', '=', $this->term)->with(['m17tb']);
    }

    public function t27tbs()
    {
        return $this->hasMany('App\Models\T27tb', 'class', 'class')->where('term', '=', $this->term);
    }

    public function t52tbs()
    {
        return $this->hasMany('App\Models\T52tb', 'class', 'class')->where('term', '=', $this->term);
    }

    public function t13tbs()
    {
        return $this->hasMany('App\Models\T13tb', 'class', 'class')->where('term', '=', $this->term)->with(['m02tb'])->orderby('no');
    }

    public function grade_main_options()
    {
        return $this->hasMany('App\Models\Grade_main_option', 'class', 'class')->where('term', '=', $this->term)->orderBy('id');
    }

    public function t13tbsForFormal()
    {
        return $this->hasMany('App\Models\T13tb', 'class', 'class')
                    ->where('term', '=', $this->term)
                    ->where('status', '=', 1)
                    ->with(['m02tb'])
                    ->orderby('no');
    }

    public function elearn_classes()
    {
        return $this->hasMany('App\Models\Elearn_class', 'class', 'class')->where('term', '=', $this->term);
    }

    public function applyModifyLogForAmdin()
    {
        return $this->hasMany('App\Models\ApplyModifyLogForAmdin', 'class', 'class')->where('term', '=', $this->term);
    }

    public function t39tbs()
    {
        return $this->hasMany('App\Models\T39tb', 'class', 'class')->where('term', '=', $this->term);
    }
    // public function elearn_classes_with_history()
    // {
    //     return $this->hasMany('App\Models\Elearn_class', 'class', 'class')->where('term', '=', $this->term)->with(['elearn_historys']);
    // }

    public function specailClassFee()
    {
        return $this->hasOne('App\Models\SpecialClassFee', 'class', 'class')->where('term', '=', $this->term);
    }

}