<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Institution extends Model
{
    use SoftDeletes;

    protected $table = 'institution';

    protected $primaryKey ='institution_id';

    protected $dates = ['deleted_at'];

    protected $fillable = array('code', 'name_full', 'name', 'unit', 'liaison_1', 'phone_1', 'fax_1', 'liaison_2', 'phone_2', 'fax_2', 'zip', 'county', 'address', 'email', 'category', 'password', 'active', 'is_statistics', 'start_date', 'end_date');

    /**
     * 啟用日期民國日期
     *
     * @return string
     */
    public function getCategoryTextAttribute()
    {
        $category = (isset($this->attributes['category']))? $this->attributes['category'] : '';

        if ( ! $category) {

            return '';
        }

        return config('app.institution_category.'.$category);
    }

    /**
     * 啟用日期民國日期
     *
     * @return string
     */
    public function getStartDateTextAttribute()
    {
        $start_date = (isset($this->attributes['start_date']))? $this->attributes['start_date'] : '';

        if ( ! $start_date) {

            return '';
        }

        return str_pad(date('Y', strtotime($start_date)) - 1911, 3, '0', STR_PAD_LEFT) . date(' 年 m 月 d 日', strtotime($start_date));
    }

    /**
     * 停用日期民國日期
     *
     * @return string
     */
    public function getEndDateTextAttribute()
    {
        $end_date = (isset($this->attributes['end_date']))? $this->attributes['end_date'] : '';

        if ( ! $end_date) {

            return '';
        }

        return str_pad(date('Y', strtotime($end_date)) - 1911, 3, '0', STR_PAD_LEFT) . date(' 年 m 月 d 日', strtotime($end_date));
    }
}
