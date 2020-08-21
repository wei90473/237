<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Car_fare extends Model
{

    protected $table = 'car_fare';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = array('id', 'county', 'area', 'fare');
}