<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Spareroom extends Model
{
    protected $table = 'spareroom';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = array('class', 'term', 'staystartdate', 'stayenddate', 'week', 'sex', 'floorno', 'bedroom', 'bedno');
}