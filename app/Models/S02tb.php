<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class S02tb extends Model
{
    protected $table = 's02tb';

    public $timestamps = false;

    protected $fillable = array('edusyear' ,'edueyear' ,'gradyear' ,'pickyear' ,'picktype' ,'langyear' ,'infoyear' ,'engyear' ,'agencyyear' ,'outlectunit' ,'burlectunit' ,'inlectunit' ,'motorunit' ,'sinunit' ,'doneunit' ,'dtwounit' ,'vipunit' ,'meaunit' ,'lununit' ,'dinunit' ,'docunit' ,'spenunit' ,'mpenunit' ,'lpenunit' ,'insunit' ,'actunit' ,'carunit' ,'teaunit' ,'prizeunit' ,'birthunit' ,'unionunit' ,'setunit' ,'dishunit' ,'csdiname' ,'csdiboss' ,'csdiaddress' ,'postboss' ,'postname' ,'posttelno' ,'postfaxno' ,'offno' ,'post' ,'girono' ,'control' ,'companyno' ,'taxorgan' ,'taxcode' ,'taxno' ,'houseno' ,'taxname' ,'taxtelno' ,'taxemail' ,'deductrate1' ,'deductrate2' ,'monthly' ,'weekly' ,'buffer1' ,'buffer2' ,'board1' ,'board2' ,'board3' ,'nightsyear' ,'nighteyear' ,'errcnt' ,'reserve' ,'siteinfo' ,'siteinq' ,'siteadm' ,'insurerate' ,'remitfee', 'Logins','tmst','tmet','tast','taet','tnst','tnet','nmst','nmet','nast','naet','nnst','nnet');
}