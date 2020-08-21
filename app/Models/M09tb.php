<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class M09tb extends Authenticatable
{
    protected $table = 'm09tb';

    protected $primaryKey ='id';

    public $timestamps = false;

    protected $fillable = array('id', 'userid' ,'userpsw', 'password' ,'username' ,'email' ,'ext' ,'siteadm' ,'section' ,'sysadm' ,'signno' ,'agent1' ,'agent2' ,'agent3' ,'deptid' ,'userpwd' ,'dimission' ,'chgpswdate', 'user_group_id', 'Last_login_time', 'Last_logins', 'Logins', 'login_time', 'idno', 'login_token');

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    public function setRememberToken($value)
    {
      // not supported
    }

    public function sponsorAgents()
    {
        return $this->hasMany('App\Models\SponsorAgent', 'userid', 'userid')->with(['m09tb']);
    }
}