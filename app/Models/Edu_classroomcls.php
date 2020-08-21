<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Edu_classroomcls extends Model
{
    protected $table = 'edu_classroomcls';

    public $timestamps = false;

    protected $primaryKey = 'id';
    
    protected $casts = [
    	'code' => 'string'
    ];

    protected $fillable = array('croomclsno', 'croomclsname', 'croomclsfullname', 'printseq','description', 'classroom', 'borrow', 'link', 'summary1', 'summary2', 'auditunit', 'note');

    public function fees()
    {
        return $this->hasMany('App\Models\Edu_clsroomfee', 'clsroomno', 'croomclsno');
    }



}