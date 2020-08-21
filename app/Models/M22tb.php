<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class M22tb extends Authenticatable
{
    protected $table = 'm22tb';

    public $timestamps = false;

    protected $fillable = array('userid' ,'lname' ,'fname' ,'sex' ,'birth' ,'userorg' ,'dept' ,'position' ,'rank' ,'ecode' ,'education' ,'offtela1' ,'offtelb1' ,'offtelc1' ,'offfaxa' ,'offfaxb' ,'homtela' ,'homtelb' ,'mobiltel' ,'dgpatel' ,'email' ,'offzip' ,'homzip' ,'offaddr1' ,'offaddr2' ,'homaddr1' ,'homaddr2' ,'chief' ,'personnel' ,'aborigine' ,'vegan' ,'handicap' ,'usertype1' ,'usertype2' ,'usertype3' ,'selfid' ,'chfcod' ,'popesn' ,'rcod1b' ,'rcod1e' ,'rcod2b' ,'rcod2e' ,'userpsw' ,'status' ,'pswerrcnt' ,'crtdate' ,'crtuserid' ,'upddate' ,'upduserid', 'account','account_type');
}