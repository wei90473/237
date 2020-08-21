<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T40tb extends Model
{
    protected $table = 't40tb';

    public $timestamps = false;

    // protected $primaryKey = 'serno';

    // protected $fillable = array('serno', 'class', 'term', 'date', 'cname', 'type');
    protected $guarded = [];
}