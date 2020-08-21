<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemCode extends Model
{
    use SoftDeletes;

    protected $table = 'system_code';

    protected $primaryKey = 'system_code_id';

    protected $dates = ['deleted_at'];

    protected $fillable = array('type', 'code', 'name', 'cost', 'category_code');

    /**
     * 分類
     *
     * @return string
     */
    public function getTypeTextAttribute()
    {
        $type = (isset($this->attributes['type']))? $this->attributes['type'] : '';

        return config('app.system_code_type.'.$type);
    }
}