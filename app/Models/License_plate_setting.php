<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class License_plate_setting extends Model
{

    protected $table = 'license_plate_setting';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = array('id', 'call', 'name', 'license_plate', 'type', 'tel1', 'tel2', 'mobile');
}