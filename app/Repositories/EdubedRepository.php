<?php
namespace App\Repositories;

use App\Models\Edu_bed;
use App\Repositories\Repository;
use DateTime;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class EdubedRepository extends Repository
{
    public function __construct(Edu_bed $eduBed)
    {
        $this->model = $eduBed;
    }  

    public function selectEmptyBed($queryData)
    {
        $sd = $queryData['staystartdate'].$queryData['staystarttime'];
        $ed = $queryData['stayenddate'].$queryData['stayendtime'];
        $floorno1 = $queryData['floorno'];

        if($queryData['sex'] == '1'){
            $sex = 'M';
        } else if($queryData['sex'] == '1'){
            $sex = 'F';
        }

        $wsql="where
                edu_bed.isuse='1' 
                and edu_floor.stayflag='1'
                and q1.bedno is null";

        $sql="select edu_floor.floorno,edu_floor.floorname,edu_bed.bedroom,edu_bed.roomname,edu_bed.bedno,substring(edu_bed.bedno,-1,1) as bedidx, '' as place
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
                and bedno <>'') q1 on q1.bedno=edu_bed.bedno";

        if (!empty($sex)){
            $sql.=" left join 
                (select edu_bed.bedroom from t04tb 
                inner join t13tb on t04tb.class = t13tb.class and t04tb.term = t13tb.term
                inner join m02tb on t13tb.idno = m02tb.idno and m02tb.sex <> '{$sex}'
                inner join edu_bed on t13tb.bedno=edu_bed.bedno where
                ((CONCAT(t04tb.staystartdate,t04tb.staystarttime) between '{$sd}' and '{$ed}') or
                (CONCAT(t04tb.stayenddate,t04tb.stayendtime) between '{$sd}' and '{$ed}')  or
                (CONCAT(t04tb.staystartdate,t04tb.staystarttime)<'{$sd}' and CONCAT(t04tb.stayenddate,t04tb.stayendtime)>'{$ed}') or
                (CONCAT(t04tb.staystartdate,t04tb.staystarttime)>'{$sd}' and CONCAT(t04tb.stayenddate,t04tb.stayendtime)<'{$ed}'))
                UNION
                select edu_bed.bedroom from edu_stayweeks 
                inner join edu_stayweeksdt on edu_stayweeks.class=edu_stayweeksdt.class and edu_stayweeks.term = edu_stayweeksdt.term and edu_stayweeks.week=edu_stayweeksdt.week
                inner join t13tb on edu_stayweeksdt.class=t13tb.class and edu_stayweeksdt.term=t13tb.term and edu_stayweeksdt.idno=t13tb.idno
                inner join m02tb on t13tb.idno = m02tb.idno and m02tb.sex <> '{$sex}'
                inner join edu_bed on edu_stayweeksdt.bedno=edu_bed.bedno where
                ((CONCAT(edu_stayweeks.staystartdate,edu_stayweeks.staystarttime) between '{$sd}' and '{$ed}') or
                (CONCAT(edu_stayweeks.stayenddate,edu_stayweeks.stayendtime) between '{$sd}' and '{$ed}')  or
                (CONCAT(edu_stayweeks.staystartdate,edu_stayweeks.staystarttime) <'{$sd}' and CONCAT(edu_stayweeks.stayenddate,edu_stayweeks.stayendtime) >'{$ed}') or
                (CONCAT(edu_stayweeks.staystartdate,edu_stayweeks.staystarttime) >'{$sd}' and CONCAT(edu_stayweeks.stayenddate,edu_stayweeks.stayendtime) <'{$ed}'))
                ) q2 on q2.bedroom=edu_bed.bedroom ";
            $wsql.=" and q2.bedroom is null";
        }       

        $sql.=' '.$wsql;

        $sql .= " and edu_bed.bedroom not in (select bedroom from spareroom where (staystartdate between '{$sd}' and '{$ed}') or (stayenddate between '{$sd}' and '{$ed}') or (staystartdate < '{$sd}' and stayenddate > '{$ed}') or (staystartdate > '{$sd}' and stayenddate < '{$ed}'))";


        if (!empty($floorno1)){
            $sql.=" and edu_floor.floorno = {$floorno1}"; 
        }

        $sql.=" order by edu_floor.floorno,edu_floor.floorname,edu_bed.bedroom,edu_bed.roomname";
       
        $query = DB::select( DB::raw($sql) );
        $page = Paginator::resolveCurrentPage("page");
        $perPage = 10; //實際每頁筆數
        $offset = ($page * $perPage) - $perPage;

        $query2 = collect(array_slice($query, $offset, $perPage, true))->values();

        $data = new LengthAwarePaginator($query2, count($query), $perPage, $page, ['path' =>  Paginator::resolveCurrentPath()]);

        return $data;

    }

    public function getEmptyBed($queryData)
    {
        $sd = $queryData['staystartdate'].$queryData['staystarttime'];
        $ed = $queryData['stayenddate'].$queryData['stayendtime'];
        $floorno1 = $queryData['floorno'];

        if($queryData['sex'] == '1'){
            $sex = 'M';
        } else if($queryData['sex'] == '1'){
            $sex = 'F';
        }

        $wsql="where
                edu_bed.isuse='1' 
                and edu_floor.stayflag='1'
                and q1.bedno is null";

        $sql="select edu_floor.floorno,edu_floor.floorname,edu_bed.bedroom,edu_bed.roomname,count(*) as cnt
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
                and bedno <>'') q1 on q1.bedno=edu_bed.bedno";

        if (!empty($sex)){
            $sql.=" left join 
                (select edu_bed.bedroom from t04tb 
                inner join t13tb on t04tb.class = t13tb.class and t04tb.term = t13tb.term
                inner join m02tb on t13tb.idno = m02tb.idno and m02tb.sex <> '{$sex}'
                inner join edu_bed on t13tb.bedno=edu_bed.bedno where
                ((CONCAT(t04tb.staystartdate,t04tb.staystarttime) between '{$sd}' and '{$ed}') or
                (CONCAT(t04tb.stayenddate,t04tb.stayendtime) between '{$sd}' and '{$ed}')  or
                (CONCAT(t04tb.staystartdate,t04tb.staystarttime)<'{$sd}' and CONCAT(t04tb.stayenddate,t04tb.stayendtime)>'{$ed}') or
                (CONCAT(t04tb.staystartdate,t04tb.staystarttime)>'{$sd}' and CONCAT(t04tb.stayenddate,t04tb.stayendtime)<'{$ed}'))
                UNION
                select edu_bed.bedroom from edu_stayweeks 
                inner join edu_stayweeksdt on edu_stayweeks.class=edu_stayweeksdt.class and edu_stayweeks.term = edu_stayweeksdt.term and edu_stayweeks.week=edu_stayweeksdt.week
                inner join t13tb on edu_stayweeksdt.class=t13tb.class and edu_stayweeksdt.term=t13tb.term and edu_stayweeksdt.idno=t13tb.idno 
                inner join m02tb on t13tb.idno = m02tb.idno and m02tb.sex <> '{$sex}'
                inner join edu_bed on edu_stayweeksdt.bedno=edu_bed.bedno where
                ((CONCAT(edu_stayweeks.staystartdate,edu_stayweeks.staystarttime) between '{$sd}' and '{$ed}') or
                (CONCAT(edu_stayweeks.stayenddate,edu_stayweeks.stayendtime) between '{$sd}' and '{$ed}')  or
                (CONCAT(edu_stayweeks.staystartdate,edu_stayweeks.staystarttime) <'{$sd}' and CONCAT(edu_stayweeks.stayenddate,edu_stayweeks.stayendtime) >'{$ed}') or
                (CONCAT(edu_stayweeks.staystartdate,edu_stayweeks.staystarttime) >'{$sd}' and CONCAT(edu_stayweeks.stayenddate,edu_stayweeks.stayendtime) <'{$ed}'))
                ) q2 on q2.bedroom=edu_bed.bedroom ";
            $wsql.=" and q2.bedroom is null";
        }       

        $sql.=$wsql;

        $sql .= " and edu_bed.bedroom not in (select bedroom from spareroom where (staystartdate between '{$sd}' and '{$ed}') or (stayenddate between '{$sd}' and '{$ed}') or (staystartdate < '{$sd}' and stayenddate > '{$ed}') or (staystartdate > '{$sd}' and stayenddate < '{$ed}'))";

        if (!empty($floorno1)){
            $sql.=" and edu_floor.floorno = {$floorno1}"; 
        }

        $sql.=" group by edu_floor.floorno,edu_floor.floorname,edu_bed.bedroom,edu_bed.roomname";        
        $sql.=" order by edu_floor.floorno,edu_floor.floorname,edu_bed.bedroom,edu_bed.roomname";

        $query = DB::select( DB::raw($sql) );
        $page = Paginator::resolveCurrentPage("page");
        $perPage = 10; //實際每頁筆數
        $offset = ($page * $perPage) - $perPage;

        $query2 = collect(array_slice($query, $offset, $perPage, true))->values();

        $data = new LengthAwarePaginator($query2, count($query), $perPage, $page, ['path' =>  Paginator::resolveCurrentPath()]);

        return $data;
    }

    public function get_emptybed($startdate,$enddate,$buildno,$bedno_from,$bedno_end,$sex,$onlyOne='')
    {
        $sd=$startdate;
        $ed=$enddate;

        $sql="select edu_floor.floorno,edu_floor.floorname,edu_bed.bedroom,edu_bed.roomname,edu_bed.bedno,substring(edu_bed.bedno,-1,1) as bedidx
                from edu_bed 
                inner join edu_floor on edu_bed.floorno=edu_floor.floorno
                where
                edu_bed.isuse='1' 
                and edu_floor.stayflag='1'
                and edu_bed.bedno not in 
                (select bedno from t04tb inner join t13tb on t04tb.class = t13tb.class and t04tb.term = t13tb.term where
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
                and bedno <>''
                ) ";

        $sql .= " and edu_bed.bedroom not in (select bedroom from spareroom where (staystartdate between '{$sd}' and '{$ed}') or (stayenddate between '{$sd}' and '{$ed}') or (staystartdate < '{$sd}' and stayenddate > '{$ed}') or (staystartdate > '{$sd}' and stayenddate < '{$ed}'))";

        if (!empty($sex)){         
            $sql.=" and edu_bed.bedroom not in
                (select edu_bed.bedroom from t04tb 
                inner join t13tb on t04tb.class = t13tb.class and t04tb.term = t13tb.term 
                inner join m02tb on t13tb.idno = m02tb.idno and m02tb.sex <> '{$sex}'
                inner join edu_bed on t13tb.bedno=edu_bed.bedno where
                ((CONCAT(t04tb.staystartdate,t04tb.staystarttime) between '{$sd}' and '{$ed}') or
                (CONCAT(t04tb.stayenddate,t04tb.stayendtime) between '{$sd}' and '{$ed}')  or
                (CONCAT(t04tb.staystartdate,t04tb.staystarttime)<'{$sd}' and CONCAT(t04tb.stayenddate,t04tb.stayendtime)>'{$ed}') or
                (CONCAT(t04tb.staystartdate,t04tb.staystarttime)>'{$sd}' and CONCAT(t04tb.stayenddate,t04tb.stayendtime)<'{$ed}'))
                UNION
                select edu_bed.bedroom from edu_stayweeks 
                inner join edu_stayweeksdt on edu_stayweeks.class=edu_stayweeksdt.class and edu_stayweeks.term = edu_stayweeksdt.term and edu_stayweeks.week=edu_stayweeksdt.week
                inner join t13tb on edu_stayweeksdt.class=t13tb.class and edu_stayweeksdt.term=t13tb.term and edu_stayweeksdt.idno=t13tb.idno 
                inner join m02tb on t13tb.idno = m02tb.idno and m02tb.sex <> '{$sex}'
                inner join edu_bed on edu_stayweeksdt.bedno=edu_bed.bedno where
                ((CONCAT(edu_stayweeks.staystartdate,edu_stayweeks.staystarttime) between '{$sd}' and '{$ed}') or
                (CONCAT(edu_stayweeks.stayenddate,edu_stayweeks.stayendtime) between '{$sd}' and '{$ed}')  or
                (CONCAT(edu_stayweeks.staystartdate,edu_stayweeks.staystarttime)<'{$sd}' and CONCAT(edu_stayweeks.stayenddate,edu_stayweeks.stayendtime)>'{$ed}') or
                (CONCAT(edu_stayweeks.staystartdate,edu_stayweeks.staystarttime)>'{$sd}' and CONCAT(edu_stayweeks.stayenddate,edu_stayweeks.stayendtime)<'{$ed}'))
                )";
        }

        if ($onlyOne == 'Y'){         
            $sql.=" and edu_bed.bedroom not in
                (select edu_bed.bedroom from t04tb 
                inner join t13tb on t04tb.class = t13tb.class and t04tb.term = t13tb.term 
                inner join m02tb on t13tb.idno = m02tb.idno
                inner join edu_bed on t13tb.bedno=edu_bed.bedno where
                ((CONCAT(t04tb.staystartdate,t04tb.staystarttime) between '{$sd}' and '{$ed}') or
                (CONCAT(t04tb.stayenddate,t04tb.stayendtime) between '{$sd}' and '{$ed}')  or
                (CONCAT(t04tb.staystartdate,t04tb.staystarttime)<'{$sd}' and CONCAT(t04tb.stayenddate,t04tb.stayendtime)>'{$ed}') or
                (CONCAT(t04tb.staystartdate,t04tb.staystarttime)>'{$sd}' and CONCAT(t04tb.stayenddate,t04tb.stayendtime)<'{$ed}'))
                UNION
                select edu_bed.bedroom from edu_stayweeks 
                inner join edu_stayweeksdt on edu_stayweeks.class=edu_stayweeksdt.class and edu_stayweeks.term = edu_stayweeksdt.term and edu_stayweeks.week=edu_stayweeksdt.week
                inner join t13tb on edu_stayweeksdt.class=t13tb.class and edu_stayweeksdt.term=t13tb.term and edu_stayweeksdt.idno=t13tb.idno 
                inner join m02tb on t13tb.idno = m02tb.idno
                inner join edu_bed on edu_stayweeksdt.bedno=edu_bed.bedno where
                ((CONCAT(edu_stayweeks.staystartdate,edu_stayweeks.staystarttime) between '{$sd}' and '{$ed}') or
                (CONCAT(edu_stayweeks.stayenddate,edu_stayweeks.stayendtime) between '{$sd}' and '{$ed}')  or
                (CONCAT(edu_stayweeks.staystartdate,edu_stayweeks.staystarttime)<'{$sd}' and CONCAT(edu_stayweeks.stayenddate,edu_stayweeks.stayendtime)>'{$ed}') or
                (CONCAT(edu_stayweeks.staystartdate,edu_stayweeks.staystarttime)>'{$sd}' and CONCAT(edu_stayweeks.stayenddate,edu_stayweeks.stayendtime)<'{$ed}'))
                )";
        }

        if (!empty($buildno)){
           $sql.=" and edu_floor.floorno='{$buildno}'";  
        }

        if (!empty($bedno_from)){
            $sql.=" and edu_bed.bedroom>='{$bedno_from}'";        
        }
        
        if (!empty($bedno_end)){
            $sql.=" and edu_bed.bedroom<='{$bedno_end}'";        
        }

        if($onlyOne == 'Y'){
            $sql .= " group by edu_bed.bedroom";
        }

        $sql.=" order by edu_bed.bedno";
        
        $data = DB::select($sql);

        return $data;
    }

    public function getBedroomCount($floorno,$bedroom)
    {
        $model = $this->model->selectRaw('count(1) as cnt')
                             ->where('floorno','=',$floorno)
                             ->where('bedroom','=',$bedroom)
                             ->where('isuse','=',DB::raw('1'));

                            
        $data = $model->get();
        
        return $data[0]['cnt'];
    }

    public function getBedInfo()
    {
        $data = $this->model->selectRaw('floorno,bedroom,count(0) AS bednum')
                             ->where('isuse','=',DB::raw('1'))
                             ->groupBy('floorno','bedroom')
                             ->get();

        return $data;
    }

}