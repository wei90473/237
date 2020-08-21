<?php
namespace App\Repositories;

use App\Models\Edu_floor;
use App\Repositories\Repository;
use DateTime;
use DB;

class EdufloorRepository extends Repository
{
    public function __construct(Edu_floor $eduFloor)
    {
        $this->model = $eduFloor;
    }  

    public function getAllFloors()
    {
    	$model = $this->model->selectRaw('edu_floor.*,edu_classroomcls.croomclsname');
    	$model->leftJoin('edu_classroomcls', 'edu_classroomcls.croomclsno', '=', 'edu_floor.croomclsno');
    	$model->orderByRaw('edu_floor.floorno');
  
        $data = $model->get();

        return $data;
    }

    public function getFloors($queryData)
    {
    	$sd = $queryData['staystartdate'].$queryData['staystarttime'];
        $ed = $queryData['stayenddate'].$queryData['stayendtime'];

    	$wsql="where
                edu_bed.isuse='1' 
                and edu_floor.stayflag='1'
                and q1.bedno is null";

        $sql="select edu_floor.floorno,edu_floor.floorname,count(*) as cnt
                from edu_bed 
                inner join edu_floor on edu_bed.floorno=edu_floor.floorno
                left join (select bedno from t04tb inner join t13tb on t04tb.class = t13tb.class and t04tb.term = t13tb.term where
                ((CONCAT(t04tb.staystartdate,t04tb.staystarttime) between '{$sd}' and '{$ed}') or
                (CONCAT(t04tb.stayenddate,t04tb.stayendtime) between '{$sd}' and '{$ed}')  or
                (CONCAT(t04tb.staystartdate,t04tb.staystarttime)<'{$sd}' and CONCAT(t04tb.stayenddate,t04tb.stayendtime)>'{$ed}') or
                (CONCAT(t04tb.staystartdate,t04tb.staystarttime)>'{$sd}' and CONCAT(t04tb.stayenddate,t04tb.stayendtime)<'{$ed}'))
                and bedno <>''
                UNION
                select bedno from edu_loansroom where
                ((edu_loansroom.startdate between '{$sd}' and '{$ed}') or
                (edu_loansroom.enddate between {$sd} and {$ed})  or
                (edu_loansroom.startdate<'{$sd}' and edu_loansroom.enddate>'{$ed}') or
                (edu_loansroom.startdate>'{$sd}' and edu_loansroom.enddate<'{$ed}'))
                and bedno <>''
                UNION
				select bedno from edu_stayweeks inner join edu_stayweeksdt on edu_stayweeks.class=edu_stayweeksdt.class and edu_stayweeks.term = edu_stayweeksdt.term and edu_stayweeks.week=edu_stayweeksdt.week where
                ((CONCAT(edu_stayweeks.staystartdate,edu_stayweeks.staystarttime) between '{$sd}' and '{$ed}') or
                (CONCAT(edu_stayweeks.stayenddate,edu_stayweeks.stayendtime) between '{$sd}' and '{$ed}')  or
                (CONCAT(edu_stayweeks.staystartdate,edu_stayweeks.staystarttime)<'{$sd}' and CONCAT(edu_stayweeks.stayenddate,edu_stayweeks.stayendtime)>'{$ed}') or
                (CONCAT(edu_stayweeks.staystartdate,edu_stayweeks.staystarttime)>'{$sd}' and CONCAT(edu_stayweeks.stayenddate,edu_stayweeks.stayendtime)<'{$ed}'))
                and bedno <>'') q1 on q1.bedno=edu_bed.bedno ";
        
        $sql.=$wsql;

        $sql.=" group by edu_floor.floorno,edu_floor.floorname";        
        $sql.=" order by edu_floor.floorno,edu_floor.floorname";

        $data = DB::select($sql);

        return $data;
    }

    public function getFloorList()
    {
    	$model = $this->model->selectRaw('distinct substring(bedno,1,2) as bno, edu_floor.floorname, edu_floor.floorno');
    	$model->Join('edu_bed', 'edu_floor.floorno', '=', 'edu_bed.floorno');
    	$model->where('edu_bed.isuse','=',DB::RAW(1));
    	$model->orderByRaw('edu_floor.floorname');
    	$data = $model->get();

        return $data;
    }
}