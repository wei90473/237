<?php 
namespace App\Repositories;

use App\Models\T49tb;
use App\Repositories\Repository;

class T49tbRepository extends Repository{
    public function __construct(T49tb $t49tb)
    {
        $this->model = $t49tb;
    }  

    public function getConclusionInfo($t04tb_info)
    {
        return $this->model->selectRaw("ifnull(sum(t67tb.fee), 0) docamt")
                           ->join('t67tb', function ($query){
                                $query->on('t67tb.class', '=', 't49tb.class')
                                      ->on('t67tb.class', '=', 't49tb.class');
                           })
                           ->where('t49tb.class', '=', $t04tb_info['class'])
                           ->where('t49tb.term', '=', $t04tb_info['term'])
                           ->where(function($query){
                                $query->where('t49tb.paiddate', '<>', '')
                                      ->orWhereNull('t49tb.paiddate');
                           })
                           ->first();
    }
}
