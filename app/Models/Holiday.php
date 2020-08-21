<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Holiday extends Model
{
    use SoftDeletes;

    protected $table = 'holiday';

    protected $primaryKey ='holiday_id';

    protected $dates = ['deleted_at'];

    protected $fillable = array('name', 'date');

    /**
     * 民國日期
     *
     * @return string
     */
    public function getDateTextAttribute()
    {
        $date = (isset($this->attributes['date']))? $this->attributes['date'] : '';

        if ( ! $date) {

            return '';
        }

        return str_pad(date('Y', strtotime($date)) - 1911, 3, '0', STR_PAD_LEFT) . date(' 年 m 月 d 日', strtotime($date));
    }
}
