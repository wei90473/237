<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TTL_mail extends Model
{

    protected $table = 'TTL_mail';

    public $timestamps = false;

    // protected $primaryKey = 'id';

    protected $fillable = array('class', 'term', 'subject', 'mail_list', 'editor', 'date');
}