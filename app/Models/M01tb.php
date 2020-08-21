<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class M01tb extends Model
{
    protected $table = 'm01tb';

    public $timestamps = false;

    protected $primaryKey = 'serno';

    protected $fillable = array('idno', 'lname', 'fname', 'cname', 'rname','ename', 'sex', 'birth', 'idkind', 'citizen', 'education', 'dept', 'position', 'offtela1', 'offtelb1', 'offtelc1', 'offtela2', 'offtelb2', 'offtelc2', 'homtela', 'homtelb', 'offfaxa', 'offfaxb', 'homfaxa', 'homfaxb', 'mobiltel', 'email', 'offzip', 'homzip', 'regzip', 'send', 'offaddress', 'homaddress', 'regaddress', 'liaison', 'bank', 'bankcode', 'bankno', 'bankaccname', 'post', 'postcode', 'postno', 'transfor', 'passport', 'datadate', 'kind', 'expert', 'publicly', 'publish', 'notify', 'experience', 'award', 'major1', 'major2', 'major3', 'major4', 'major5', 'major6', 'major7', 'major8', 'major9', 'major10', 'remark', 'serno', 'authority', 'insurekind1', 'insurekind2', 'm01tbcol', 'update_date');
}