<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChangeTrainingPlanFiles extends Model
{
 
    protected $table = 'changetraining_plan_files';

    public $timestamps = false;

    protected $fillable = array('id', 'class', 'filepath', 'filename', 'delete','modified_time','modified_user');

}
