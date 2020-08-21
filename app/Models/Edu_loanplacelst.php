<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Edu_loanplacelst extends Model
{
    protected $table = 'edu_loanplacelst';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = array('applyno', 'startdate', 'enddate', 'timestart','timeend', 'croomclsno', 'placenum', 'fee', 'hday', 'nday', 'nfee', 'hfee'
                                ,'description', 'ndiscount', 'hdiscount');
}