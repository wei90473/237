<?php 
namespace App\Repositories;

use App\Models\T51tb;
use App\Repositories\Repository;
use DB;

class T51tbRepository extends Repository{
    public function __construct(T51tb $t51tb)
    {
        $this->model = $t51tb;
    }  

    public function getByT04tb($t04tb_info)
    {
        return $this->model->select([
                                "t51tb.*", 
                                "m17tb.enrollorg as m17tb_enrollorg", 
                                "m17tb.enrollname as m17tb_enrollname", 
                                "m17tb.grade as m17tb_grade",
                                "m17tb.organ as m17tb_organ",
                                "m17tb.uporgan as m17tb_uporgan"
                            ])
                           ->where($t04tb_info)
                           ->join('m17tb', 'm17tb.enrollorg', '=', 't51tb.organ')
                           ->join('m13tb', 'm13tb.organ', '=', 'm17tb.organ')
                           ->orderBy('m13tb.rank')
                           ->orderBy('m17tb.enrollorg')
                        //    ->orderBy('m17tb.grade')
                           ->get();
    }

    public function getByIn($conditions)
    {
        $model = $this->model->select("*");
        foreach ($conditions as $class => $terms){
            $model->orWhere(function($query) use($class, $terms){
                $query->where('class', '=', $class)
                      ->WhereIn('term', $terms);
            });
        }
        return $model->get();
    }

    public function getAssignedOtherOrgan($classes){
        $model = $this->model->selectRaw("t51tb.class, t51tb.term, count(*)");

        $model->Where(function($query) use($classes){
            foreach ($classes as $class => $terms){
                foreach ($terms as $term){
                    $query->orWhere(compact(['class', 'term']));
                }
            }
        });

        $model->join('m17tb', function($join){
            $join->on('m17tb.enrollorg', '=', 't51tb.organ');
            $join->on('m17tb.grade', '>', DB::raw("1"));
        });
        $model->groupBy(['t51tb.class', 't51tb.term']);
        return $model->get();
    }

    public function getByT04tbs($t04tb_keys)
    {
        $model = $this->model->select(["*"]);

        foreach ($t04tb_keys as $class => $terms){
            foreach ($terms as $term){
                $model->orWhere(function($query) use($class, $term){
                    $query->where(compact(['class', 'term']));
                });
            }
        }

        return $model->get();
    }
}
