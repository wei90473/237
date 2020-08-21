<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Edu_stayweeksdt extends Model
{
    protected $table = 'edu_stayweeksdt';

    public $timestamps = false;

    protected $primaryKey = 'id';
    protected $fillable = array('annualplan_id', 'class', 'term', 'week', 'idno', 'bedno', 'floorno');
}