<?php
namespace App\Repositories;

use App\Models\T01tb;
use DB;
use App\Repositories\Repository;

class T01tbRepository extends Repository
{
    public function __construct(T01tb $t01tb)
    {
        $this->model = $t01tb;
    }  

    public function get($queryData = null, $paginate = true, $select = "*", $with = [])
    {
        $t01tbs = $this->model->selectRaw($select);
        $t01tbs->with($with);
        if(!empty($queryData['yerly'])){
            $t01tbs->where('class', 'LIKE', "{$queryData['yerly']}%");
        }

        if(!empty($queryData['class'])){
            $t01tbs->Where('class', 'LIKE', "%{$queryData['class']}%");
        }

        // if(!empty($queryData['name'])){
        //     $t01tbs->Where('name', 'LIKE', "%{$queryData['class_or_name']}%");
        // }

        if(!empty($queryData['class_name'])){
            $t01tbs->Where('name', 'LIKE', "%{$queryData['class_name']}%");
        }
        
        if(!empty($queryData['branch'])){
            $t01tbs->Where('branch', '=', $queryData['branch']);
        }

        if (isset($queryData['yerly'])){
            $queryData['yerly'] = str_pad($queryData['yerly'], 3, '0', STR_PAD_LEFT);
            $t01tbs->where('class', 'LIKE', "{$queryData['yerly']}%");
        }


        if(!empty($queryData['class_or_name'])){
            $t01tbs->where(function($query) use($queryData){
                $query->where('class', 'LIKE', "%{$queryData['class_or_name']}%")
                      ->orWhere('name', 'LIKE', "%{$queryData['class_or_name']}%");
            });
        }

        if ($paginate){
            $paginate_qty = (isset($queryData['_paginate_qty']) && $queryData['_paginate_qty']) ? $queryData['_paginate_qty'] : 10;
            return $t01tbs->paginate($paginate_qty);
        }else{
            return $t01tbs->get();
        }            
        
    }

    public function getrptPPrptSub8($sdate,$edate){
        $sql = sprintf("SELECT
                            t01tb.yerly,
                            t04tb.class as classno,
                            t01tb.`name` as classname,
                            t04tb.term as period,
                            t04tb.sdate as startdate,
                            t04tb.edate as enddate,
                            t01tb.trainday as trainingday,
                            0 AS `week`,
                            t04tb.fincnt as rosternum,
                            m02tb.sex,
                            t13tb.bedno,
                            t13tb.dorm as stay,
                            edu_floor.floorno,
                            edu_floor.floorname,
                            edu_bed.roomname,
                            edu_bed.bedroom,
                            m09tb.username AS counselorname
                        FROM
                            t01tb
                            INNER JOIN t04tb ON t01tb.class = t04tb.class
                            INNER JOIN t13tb ON t13tb.class = t04tb.class 
                            AND t13tb.term = t04tb.term
                            AND t13tb.dorm = 'Y' 
                            AND t13tb.`status` IN ('1', '2')
                            INNER JOIN m02tb on t13tb.idno = m02tb.idno
                            INNER JOIN edu_bed ON edu_bed.bedno = t13tb.bedno
                            LEFT JOIN edu_floor ON edu_floor.floorno = edu_bed.floorno
                            LEFT JOIN m09tb ON t04tb.sponsor = m09tb.userid 
                        WHERE
                            t04tb.site_branch = 2
                            AND (((
                                    t04tb.staystartdate BETWEEN '%s' 
                                    AND '%s' 
                                    ) 
                                OR (
                                    '%s' BETWEEN t04tb.staystartdate
                                AND t04tb.stayenddate)) or ((
                                    t04tb.sdate BETWEEN '%s' 
                                    AND '%s' 
                                    ) 
                                OR (
                                    '%s' BETWEEN  t04tb.sdate  
                                AND t04tb.edate))) UNION
                        SELECT
                            t01tb.yerly,
                            t04tb.class as classno,
                            t01tb.`name` as classname,
                            t04tb.term as period,
                            t04tb.sdate as startdate,
                            t04tb.edate as enddate,
                            t01tb.trainday as trainingday,
                            edu_stayweeks.`week`,
                            t04tb.fincnt as rosternum,
                            m02tb.sex,
                            edu_stayweeksdt.bedno,
                            t13tb.dorm as stay,
                            edu_floor.floorno,
                            edu_floor.floorname,
                            edu_bed.roomname,
                            edu_bed.bedroom,
                            m09tb.username AS counselorname
                        FROM
                            t01tb
                            INNER JOIN t04tb ON t01tb.class = t04tb.class
                            INNER JOIN edu_stayweeks ON edu_stayweeks.class = t04tb.class and edu_stayweeks.term = t04tb.term
                            INNER JOIN t13tb ON t13tb.class = t04tb.class 
                            and t13tb.term = t04tb.term
                            AND t13tb.dorm = 'Y'  
                            AND t13tb.`status` IN ('1', '2')
                            INNER JOIN edu_stayweeksdt ON edu_stayweeksdt.class = t04tb.class and edu_stayweeksdt.term = t04tb.term
                            AND edu_stayweeksdt.WEEK = edu_stayweeks.WEEK 
                            AND edu_stayweeksdt.idno = t13tb.idno
                            INNER JOIN m02tb on t13tb.idno = m02tb.idno
                            INNER JOIN edu_bed ON edu_bed.bedno = edu_stayweeksdt.bedno
                            LEFT JOIN edu_floor ON edu_floor.floorno = edu_bed.floorno
                            LEFT JOIN m09tb ON t04tb.counselor = m09tb.userid 
                        WHERE
                            t04tb.site_branch = 2 
                            AND ((
                                    edu_stayweeks.staystartdate BETWEEN '%s' 
                                    AND '%s' 
                                    ) 
                            OR ( '%s' BETWEEN edu_stayweeks.staystartdate AND edu_stayweeks.stayenddate )) 
                        ORDER BY
                            startdate,
                            trainingday DESC,
                            enddate,
                            classno,
                            period,
                            `week`,
                            floorno,
                            roomname,
                            bedno",$sdate,$edate,$sdate,$sdate,$edate,$sdate,$sdate,$edate,$sdate);

        $data = DB::select($sql);

        return $data;
    }
    /*
        取得已分配人數的班級
    */
    public function getINt03tb($yerly)
    {
        // return $this->model->whereExists(function($query){
        //     $query->selectRaw('class, term')
        //           ->from('t03tb')
        //           ->whereRaw('t03tb.class = t01tb.class');
        // })->where('yerly', '=', $yerly)->get();
        return $this->model->join(DB::raw('(SELECT class, term FROM t03tb GROUP BY class, term) as t03tbGroup'), 't03tbGroup.class', '=', 't01tb.class')
                           ->selectRaw('t01tb.class, t03tbGroup.term, t01tb.trainhour')
                           ->where('yerly', '=', $yerly)
                           ->get();
    }
}