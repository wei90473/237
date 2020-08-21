<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Place extends Model
{
    use SoftDeletes;

    protected $table = 'place';

    protected $primaryKey ='place_id';

    protected $dates = ['deleted_at'];

    protected $fillable = array('number', 'name', 'type', 'reservation', 'charge', 'servicefee_a', 'servicefee_b', 'servicefee_c', 'rule', 'seat', 'door');

    /**
     * 場地類型
     *
     * @return string
     */
    public function getTypeTextAttribute()
    {
        $type = (isset($this->attributes['type']))? $this->attributes['type'] : '';

        return config('app.place_type.'.$type);
    }

    /**
     * 預約類型
     *
     * @return string
     */
    public function getReservationTextAttribute()
    {
        $reservation = (isset($this->attributes['reservation']))? $this->attributes['reservation'] : '';

        return config('app.place_reservation.'.$reservation);
    }

    /**
     * 收費類型
     *
     * @return string
     */
    public function getChargeTextAttribute()
    {
        $charge = (isset($this->attributes['charge']))? $this->attributes['charge'] : '';

        return config('app.place_charge.'.$charge);
    }

    /**
     * 座位類型
     *
     * @return string
     */
    public function getSeatTextAttribute()
    {
        $seat = (isset($this->attributes['seat']))? $this->attributes['seat'] : '';

        return config('app.place_seat.'.$seat);
    }

    /**
     * 教室門口
     *
     * @return string
     */
    public function getDoorTextAttribute()
    {
        $door = (isset($this->attributes['door']))? $this->attributes['door'] : '';

        return config('app.place_door.'.$door);
    }
}
