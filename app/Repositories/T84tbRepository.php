<?php 
namespace App\Repositories;

use App\Models\T84tb;
use App\Repositories\Repository;
use DB;

class T84tbRepository extends Repository{
    public function __construct(T84tb $t84tb)
    {
        $this->model = $t84tb;
    }  

    public function get($queryData, $paginate=true)
    {
        $queryData['class'] = addslashes($queryData['class']);
        $queryData['term'] = addslashes($queryData['term']);
        $table = "SELECT t84tb_1.*, t84tb_1.timed atimed, t84tb_2.timed btimed
                FROM t84tb t84tb_1 
                LEFT JOIN t84tb t84tb_2 ON 
                    t84tb_2.status = 'B' AND 
                    t84tb_2.class = t84tb_1.class AND 
                    t84tb_2.term = t84tb_1.term AND
                    t84tb_2.dated = t84tb_1.dated AND
                    t84tb_2.idno = t84tb_1.idno	
                WHERE 
                    t84tb_1.class = '{$queryData['class']}' AND 
                    t84tb_1.term ='{$queryData['term']}' AND 
                    t84tb_1.status = 'A'
                UNION
                SELECT t84tb_1.*, t84tb_2.timed adated, t84tb_1.timed bdated
                FROM t84tb t84tb_1 
                LEFT JOIN t84tb t84tb_2 ON 
                    t84tb_2.status = 'A' AND 
                    t84tb_2.class = t84tb_1.class AND 
                    t84tb_2.term = t84tb_1.term AND 
                    t84tb_2.dated = t84tb_1.dated AND
                    t84tb_2.idno = t84tb_1.idno
                WHERE 
                    t84tb_1.class = '{$queryData['class']}' AND 
                    t84tb_1.term ='{$queryData['term']}' AND 
                    t84tb_1.status = 'B' AND 
                    t84tb_2.idno is null";
        $model = DB::table(DB::raw("( $table ) as t84tb"));

        $model = $model->select([
            "t84tb.*", "t13tb.no as t13tb_no", "m02tb.cname as m02tb_cname"
        ]);

        $model->join('m02tb', 'm02tb.idno', '=', 't84tb.idno')
              ->join('t13tb', function ($join){
                    $join->on('t13tb.class', '=', 't84tb.class')
                         ->on('t13tb.term', '=', 't84tb.term')
                         ->on('t13tb.idno', '=', 't84tb.idno');
              });

        $fields = [
            'other_equal' => [
                't84tb' => [
                    'dated', 'class', 'term'
                ],
                'm02tb' => [
                    'cname'
                ]                
            ],
            'other_like' => [
                't13tb' =>[
                    'no'
                ]
            ],
            'other_not_in' => [
                't84tb' => [
                    'status'
                ]
            ]
        ];

        $this->queryField($model, $fields, $queryData);
        $model->orderBY('t13tb_no');
        if ($paginate){
            return $model->paginate(10);
        }else{
            return $model->get();
        }
        
    }
}
