<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recommend extends Model
{
    use SoftDeletes;

    protected $table = 'recommend';

    protected $primaryKey = 'recommend_id';

    protected $dates = ['deleted_at'];

    protected $fillable = array('code', 'name', 'institution_id', 'zip', 'county', 'address', 'level', 'parents_code', 'password', 'active', 'error_count', 'personid', 'cross_region_sign');

    /**
     * 主管機關
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function institution()
    {
        return $this->belongsTo('App\Models\Institution', 'institution_id', 'institution_id');
    }
}