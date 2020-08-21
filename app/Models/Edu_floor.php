<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Edu_floor extends Model
{
    protected $table = 'edu_floor';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = array('floorno', 'floorname', 'croomclsno', 'seq','stayflag');
}