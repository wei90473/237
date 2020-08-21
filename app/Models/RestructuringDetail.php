<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestructuringDetail extends Model
{
    protected $table = 'restructuring_detail';
    protected $guarded = [];
    public $timestamps = false;

    public function m17tb()
    {
        return $this->belongsTo('App\Models\M17tb', 'enrollorg', 'enrollorg');
    }    
}