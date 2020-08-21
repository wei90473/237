<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T33tb extends Model
{
    protected $table = 't33tb';

    public $timestamps = false;

    protected $fillable = array('subject', 'author', 'email', 'institute', 'date', 'renum', 'content');
}