<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Class_mail extends Model
{

    protected $table = 'class_mail';

    public $timestamps = false;

    // protected $primaryKey = 'id';

    protected $fillable = array('class', 'term', 'title', 'mail_list', 'content', 'date');
}