<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Edu_signup extends Model
{
    protected $table = 'edu_signup';

    public $timestamps = false;

    protected $primaryKey = 'id';
    protected $guarded=['id'];
    /*protected $fillable = array('idname', 'officialname', 'manhours', 'rosternum','frosternum','frosternum','frosternum'
    ,'checkinnum','fcheckinnum','completiondtraining','completiondftraining','regstartdate','regenddate','dispstartdate','dispenddate','frosternum'
    ,'auditstartdate','auditenddate','counselor','coursehours','certhours','summary','summary1','description','rtraining'
    ,'testcourse','acaratio','liferatio','classroom','mealstartdate','mealenddate','mealenddate','mealenddate','staystartdate'
    ,'stayenddate','classification1','classification2','classification3','memo','stuseq');*/
}