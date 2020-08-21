<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T28tb extends Model
{
    protected $table = 't28tb';

    public $timestamps = false;

    protected $primaryKey = 'serno';

    protected $fillable = array('sdate', 'edate', 'title', 'link', 'url', 'content', 'type');
}