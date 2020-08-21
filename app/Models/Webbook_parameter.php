<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Webbook_parameter extends Model
{
    protected $table = 'webbook_parameter';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = array('name', 'organize', 'email', 'cre_user','cre_date');
}