<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingProcess extends Model
{
    use SoftDeletes;

    protected $table = 'training_process';

    protected $primaryKey = 'training_process_id';

    protected $dates = ['deleted_at'];

    protected $fillable = array('class', 'term', 'serno', 'comment', 'addcourse', 'delcourse', 'wholeval', 'willing', 'othercom');
}