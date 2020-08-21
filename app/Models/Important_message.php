<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Important_message extends Model
{
    protected $table = 'important_message';

    public $timestamps = false;

    protected $primaryKey = 'serno';

    protected $fillable = array('position', 'for', 'title', 'opener','launch', 'discontinue', 'content');
}