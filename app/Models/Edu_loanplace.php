<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Edu_loanplace extends Model
{
    protected $table = 'edu_loanplace';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = array('applyno', 'applydate', 'orgname', 'title','applyuser', 'num', 'mstay', 'fstay', 'reason', 'tel', 'fax', 'cellphone', 'chief1'
                                , 'chief2', 'status', 'reason2', 'processdate', 'discount1', 'discount2', 'paydate', 'mlist', 'flist', 'orgcode1', 'orgcode2'
                                , 'locked', 'description', 'loginip', 'email', 'passwd', 'payuser', 'applykind', 'alphabet', 'addr', 'summary', 'waterno'
                                , 'discounttype');
}