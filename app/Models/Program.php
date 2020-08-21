<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use SoftDeletes;

    protected $table = 'program';

    protected $primaryKey ='program_id';

    protected $dates = ['deleted_at'];

    protected $fillable = array('code', 'name', 'is_recode');

    /**
     * 異動記錄註記
     *
     * @return string
     */
    public function getIsRecodeTextAttribute()
    {
        $isRecode = (isset($this->attributes['is_recode']))? $this->attributes['is_recode'] : '';

        if ($isRecode === '') {

            return '';
        }

        return ($isRecode)? '記錄' : '不記錄';
    }
}
