<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class DemandTransactDates extends Authenticatable
{
    protected $table = 'demand_transact_dates';

    public $timestamps = false;

    protected $fillable = array( 'id','demand_id','sdate','edate');
}




