<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class M25tb extends Model
{
    protected $table = 'm25tb';

    public $timestamps = false;
    public $primaryKey = "site";
    
    protected $fillable = array('site', 'name');
}