<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OnlineApplyOrgan extends Model
{
    protected $table = 'online_apply_organs';
    protected $casts = ['enrollorg' => 'string'];
    // public $primaryKey;
    
    protected $fillable = array('class', 'term', 'enrollorg', 'officially_enroll', 'secondary_enroll', 'open_belong_apply');
    public function t04tb()
    {
        return $this->belongsTo('App\Models\T04tb', 'class', 'class')->where('term', '=', $this->term);
    }

    public function m17tb()
    {
        return $this->belongsTo('App\Models\M17tb', 'enrollorg', 'enrollorg');
    }
}