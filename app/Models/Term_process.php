<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Term_process extends Model
{

    protected $table = 'term_process';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = array('id', 'class_process_id', 'class_process_job_id', 'class', 'term', 'complete');
}