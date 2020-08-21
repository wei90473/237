<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Managers extends Authenticatable
{
    use SoftDeletes;

    protected $table = 'managers';

    protected $primaryKey ='managers_id';

    protected $dates = ['deleted_at'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'account', 'password', 'recommend_id', 'name', 'contact_unit', 'phone_area', 'phone', 'ext', 'email', 'create_managers_id', 'is_liaison', 'permission_id', 'active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
