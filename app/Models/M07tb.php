<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class M07tb extends Model
{
    protected $table = 'm07tb';

    public $timestamps = false;

    protected $primaryKey = '';

    protected $fillable = array('agency','name','telnoa','telnob','telnoc','faxnoa','faxnob','url','address','chief','cposition','ctelnoa','ctelnob','ctelnoc','cfaxnoa','cfaxnob','cmobiltel','cemail','assistant1','aposition1','atelnoa1','atelnob1','atelnoc1','afaxnoa1','afaxnob1','amobiltel1','aemail1','assistant2','aposition2','atelnoa2','atelnob2','atelnoc2','afaxnoa2','afaxnob2','amobiltel2','aemail2','liaison1','lposition1','ltelnoa1','ltelnob1','ltelnoc1','lfaxnoa1','lfaxnob1','lmobiltel1','lemail1','liaison2','lposition2','ltelnoa2','ltelnob2','ltelnoc2','lfaxnoa2','lfaxnob2','lmobiltel2','lemail2','liaison3','lposition3','ltelnoa3','ltelnob3','ltelnoc3','lfaxnoa3','lfaxnob3','lmobiltel3','lemail3','liaison4','lposition4','ltelnoa4','ltelnob4','ltelnoc4','lfaxnoa4','lfaxnob4','lmobiltel4','lemail4','liaison5','lposition5','ltelnoa5','ltelnob5','ltelnoc5','lfaxnoa5','lfaxnob5','lmobiltel5','lemail5','rank','userpsw','status','pswerrcnt','enrollorg');
}