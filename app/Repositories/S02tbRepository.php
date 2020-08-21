<?php
namespace App\Repositories;

use App\Models\S02tb;

class S02tbRepository
{
    public function __construct(S02tb $s02tb)
    {
        $this->model = $s02tb;
    }  

    public static function get($param){
        $s02tb = $this->model->first();
        if (empty($s02tb)) return false;
        return $s02tb->$param;
    }
}