<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainQuestionnaire extends Model
{
    protected $table = 'train_questionnaires';
    protected $fillable = array('setting_id' ,'type' ,'origin_name', 'path', 'created_at', 'updated_at');
}