<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T08tb extends Model
{
    protected $table = 't08tb';

    public $timestamps = false;

    protected $fillable = array('class', 'term', 'course', 'idno', 'lname', 'fname', 'cname', 'ename', 'idkind', 'dept', 'position', 'offtela1', 'offtelb1', 'offtelc1', 'offtela2', 'offtelb2', 'offtelc2', 'homtela', 'homtelb', 'offfaxa', 'offfaxb', 'homfaxa', 'homfaxb', 'mobiltel', 'liaison', 'hire');
}