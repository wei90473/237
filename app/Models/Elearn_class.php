<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Elearn_class extends Model
{
    protected $table = 'elearn_class';

    protected $primaryKey = 'id';

    protected $fillable = array('class', 'term', 'code', 'name', 'created_at', 'updated_at');

}