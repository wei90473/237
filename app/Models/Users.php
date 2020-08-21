<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Users extends Model
{
    use SoftDeletes;

    protected $table = 'users';

    protected $primaryKey = 'users_id';

    protected $dates = ['deleted_at'];

    protected $fillable = array('account', 'id', 'last_name', 'first_name', 'gender', 'birth', 'recommend_id', 'officium', 'jobtitle', 'highestdegree', 'school', 'office_zip', 'office_county', 'office_address', 'zip', 'county', 'address', 'office_phone_area', 'office_phone', 'office_ext', 'office_fax_area', 'office_fax', 'phone_area', 'phone', 'cellphone', 'phone_hr', 'email', 'student', 'lecture', 'classmate', 'supervisor', 'hr', 'indigenous', 'vegetarian', 'handicapped');

    /**
     * 電話(公)
     *
     * @return Object
     */
    public function getOfficePhoneTextAttribute()
    {
        $office_phone_area = (isset($this->attributes['office_phone_area']))? $this->attributes['office_phone_area'] : '';

        $office_phone = (isset($this->attributes['office_phone']))? $this->attributes['office_phone'] : '';

        $office_ext = (isset($this->attributes['office_ext']))? $this->attributes['office_ext'] : '';

        $result = '';

        $result .= ($office_phone_area)? '('.$office_phone_area.') ' : '';

        $result .= $office_phone;

        $result .= ($office_ext)? ' #'.$office_ext : '';

        return $result;
    }

    /**
     * 電話(宅)
     *
     * @return Object
     */
    public function getPhoneTextAttribute()
    {
        $phone_area = (isset($this->attributes['phone_area']))? $this->attributes['phone_area'] : '';

        $phone = (isset($this->attributes['phone']))? $this->attributes['phone'] : '';

        $result = '';

        $result .= ($phone_area)? '('.$phone_area.') ' : '';

        $result .= $phone;

        return $result;
    }

    /**
     * 最高學歷
     *
     * @return string
     */
    public function getHighestdegreeAttribute()
    {
        $highestdegree = (isset($this->attributes['highestdegree']))? $this->attributes['highestdegree'] : '';

        return config('app.recommend_highestdegree.'.$highestdegree);
    }
}