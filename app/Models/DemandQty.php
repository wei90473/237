<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DemandQty extends Model
{
    use SoftDeletes;

    protected $table = 'demand_qty';

    protected $primaryKey = 'demand_qty_id';

    protected $dates = ['deleted_at'];

    protected $fillable = array('demand_distribution_id', 'institution_id', 'qty_require', 'qty_quota');
}