<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SponsorAgent extends Model
{
    protected $table = 'sponsor_agents';

    protected $guarded = array('id');

    public function m09tb()
    {
        return $this->belongsTo('App\Models\M09tb', 'agent_userid', 'userid');        
    }
}