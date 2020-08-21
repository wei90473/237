<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class T27tb extends Model
{
    protected $table = 't27tb';

    // public $timestamps = false;
    // protected $fillable = ['class'];
    // protected $primaryKey = 'serno';

    protected $guarded = [];
    public function t04tb()
    {
        return $this->belongsTo('App\Models\t04tb', 'class', 'class')->where('term', '=', $this->term);
    }
}