<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T76tb extends Model
{
    protected $table = 't76tb';

    public $timestamps = false;

    protected $primaryKey = 'serno';

    protected $fillable = array('sdate', 'edate', 'title', 'content');
}