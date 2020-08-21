<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T78tb extends Model
{
    protected $table = 't78tb';

    public $timestamps = false;

    protected $primaryKey = 'serno';

    protected $fillable = array('serno', 'ansindex', 'answers', 'checknum');
}