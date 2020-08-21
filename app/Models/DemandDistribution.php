<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DemandDistribution extends Model
{
    use SoftDeletes;



    protected $table = 'demand_distribution';

    protected $primaryKey = 'demand_distribution_id';

    protected $dates = ['deleted_at'];

    protected $fillable = array('demand_survey_id', 'classes_id', 'total_qty_require', 'total_qty_quota');

    public function demand_qty()
    {
        return $this->hasMany(DemandQty::class, 'demand_distribution_id', 'demand_distribution_id');
    }
}