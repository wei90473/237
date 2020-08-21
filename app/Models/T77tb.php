<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T77tb extends Model
{
    protected $table = 't77tb';

    public $timestamps = false;

    protected $primaryKey = 'serno';

    protected $fillable = array('subject', 'sdate', 'edate');
}