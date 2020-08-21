<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T38tb extends Model
{
    protected $table = 't38tb';

    public $timestamps = false;

    protected $fillable = array('meet', 'serno','time', 'name', 'cnt', 'sponsor', 'sdate', 'edate', 'kind', 'client', 'invoice', 'remark', 'chairman', 'equip', 'applydate', 'activity', 'payer', 'address', 'type', 'liaison', 'position', 'telno', 'faxno', 'mobiltel', 'email', 'casemk', 'result', 'totalfee', 'duedate', 'notetype', 'year', 'no', 'replymk', 'replydate', 'replynote', 'prove', 'transdate','site1','branch1','branch2','branch3','site2','site3');
}