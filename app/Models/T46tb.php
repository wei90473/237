<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T46tb extends Model
{
    protected $table = 't46tb';

    public $timestamps = false;

    protected $primaryKey = 'serno';

    protected $fillable = array('serno', 'class', 'term', 'date', 'cname', 'type');
}