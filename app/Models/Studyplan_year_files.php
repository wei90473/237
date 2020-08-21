<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Studyplan_year_files extends Model
{
    protected $table = 'studyplan_year_files';

    public $timestamps = false;

    protected $fillable = array('id', 'year', 'filepath', 'filename', 'delete');

}
