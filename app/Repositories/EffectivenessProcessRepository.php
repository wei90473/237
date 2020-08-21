<?php
namespace App\Repositories;

use App\Models\T01tb;
use App\Models\T53tb;
use App\Models\T54tb;
use App\Models\T56tb;
use App\Models\T06tb;
use App\Models\M01tb;
use App\Models\T95tb;
use App\Models\T09tb;
use DB;
use App\Repositories\EffectivenessSurveyRepository;

class EffectivenessProcessRepository
{
    public function __construct(EffectivenessSurveyRepository $efs)
    {
        $this->efs = $efs;
    }
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getEffectivenessProcessList($queryData = [])
    {
        //$query = T95tb::select('id', 'class', 'term', 'times', 'serno', DB::raw('(CASE fillmk WHEN \'1\' THEN \'書面\' WHEN \'2\' THEN \'匯入\' ELSE \'\' END) AS fillmk'));
        //$query=T01tb::select('class,RTRIM(name) as name');
        $query=T01tb::select('t01tb.class','t01tb.name','t04tb.sponsor','t04tb.sdate','t04tb.edate','t04tb.client','t01tb.branch','t01tb.name','t01tb.branchname',
        't01tb.traintype','t01tb.process','t04tb.term','t54tb.times');
        $query= $query->whereExists(function($query){
           $query->select(DB::raw('*'))->from('t53tb')->whereRaw('t53tb.class=t01tb.class and t53tb.times is not null');
        });
        //dd($queryData);
        $query->join('t04tb',function($join)
        {
            $join->on('t04tb.class','=','t01tb.class');
        });

        $query->join('t53tb',function($join)
        {
            $join->on('t53tb.class','=','t01tb.class');
            $join->on('t04tb.term','=','t53tb.term');

        });


        $query->leftJoin('t54tb',function($join)
        {
            $join->on('t01tb.class','=','t54tb.class');
        });

        //year
        if ( isset($queryData['yerly']) && $queryData['yerly'] ) {
            $query->where('t01tb.yerly', $queryData['yerly']);
        }

        // 班號
        if ( isset($queryData['class']) && $queryData['class'] ) {
            $query->where('t01tb.class', 'LIKE', '%'.$queryData['class'].'%');

        }

         // 班別
        if ( isset($queryData['name']) && $queryData['name'] ) {
            $query->where('t01tb.name', 'LIKE', '%'.$queryData['name'].'%');
        }
        //分班名稱
        if ( isset($queryData['class_branch_name']) && $queryData['class_branch_name'] ) {
            $query->where('t01tb.branchname', 'LIKE', '%'.$queryData['class_branch_name'].'%');
        }

        //辦班院區
        if ( isset($queryData['branch']) && $queryData['branch'] ) {
            $query->where('t01tb.branch', 'LIKE', '%'.$queryData['branch'].'%');
        }
        //訓練性質
        if ( isset($queryData['process']) && $queryData['process'] ) {
            $query->where('t01tb.process', 'LIKE', '%'.$queryData['process'].'%');
        }
        //班別性質
        if ( isset($queryData['traintype']) && $queryData['traintype'] ) {
            $query->where('t01tb.traintype', 'LIKE', '%'.$queryData['traintype'].'%');
        }

        if ( isset($queryData['categoryone']) && $queryData['categoryone'] ) {
            $query->where('t01tb.categoryone', $queryData['categoryone']);
        }

        //班務人員
        if ( isset($queryData['sponsor']) && $queryData['sponsor'] ) {
            $query->where('t04tb.sponsor', '=', $queryData['sponsor']);
        }

        if ( isset($queryData['type']) && $queryData['type'] ) {
            //$query->where('t01tb.type', 'LIKE', '%'.$queryData['type'].'%');
            $query->where('t01tb.type', '=', $queryData['type']);

        }

        if ( isset($queryData['sitebranch']) && $queryData['sitebranch'] ) {
            $query->where('t04tb.site_branch', $queryData['sitebranch']);
        }

        if ( isset($queryData['sdate']) && $queryData['sdate'] ) {
            $queryData['sdate'] = str_pad($queryData['sdate'],7,'0',STR_PAD_LEFT);
            $query->where('t04tb.sdate', '>=', $queryData['sdate']);
        }
        if ( isset($queryData['edate']) && $queryData['edate'] ) {
            $queryData['edate'] = str_pad($queryData['edate'],7,'0',STR_PAD_LEFT);
            $query->where('t04tb.sdate', '<=', $queryData['edate']);
        }

        if ( isset($queryData['sdate2']) && $queryData['sdate2'] ) {
            $queryData['sdate2'] = str_pad($queryData['sdate2'],7,'0',STR_PAD_LEFT);
            $query->where('t04tb.edate', '>=', $queryData['sdate2']);
        }
        if ( isset($queryData['edate2']) && $queryData['edate2'] ) {
            $queryData['edate2'] = str_pad($queryData['edate2'],7,'0',STR_PAD_LEFT);
            $query->where('t04tb.edate', '<=', $queryData['edate2']);
        }

        if(isset($queryData['sdate3']) && $queryData['sdate3'] && isset($queryData['edate3']) && $queryData['edate3'] ){
            $queryData['sdate3'] = str_pad($queryData['sdate3'],7,'0',STR_PAD_LEFT);
            $queryData['edate3'] = str_pad($queryData['edate3'],7,'0',STR_PAD_LEFT);

            $query->leftJoin('t06tb', function($join)
            {
                $join->on('t04tb.class', '=', 't06tb.class')
                ->on('t04tb.term', '=', 't06tb.term');
            });
            $query->where('t06tb.date', '>=', $queryData['sdate3']);
            $query->where('t06tb.date', '<=', $queryData['edate3']);
            $query->groupBy('t06tb.class');

            //$query->distinct();
        }else{
            if ( isset($queryData['sdate3']) && $queryData['sdate3'] ) {
                $query->leftJoin('t06tb', function($join)
                {
                    $join->on('t04tb.class', '=', 't06tb.class')
                    ->on('t04tb.term', '=', 't06tb.term');
                });
                $queryData['sdate3'] = str_pad($queryData['sdate3'],7,'0',STR_PAD_LEFT);

                $query->where('t06tb.date', '>=', $queryData['sdate3']);
                //$query->distinct();
                $query->groupBy('t06tb.class');

            }
            if ( isset($queryData['edate3']) && $queryData['edate3'] ) {
                $query->leftJoin('t06tb', function($join)
                {
                    $join->on('t04tb.class', '=', 't06tb.class')
                    ->on('t04tb.term', '=', 't06tb.term');
                });
                $queryData['edate3'] = str_pad($queryData['edate3'],7,'0',STR_PAD_LEFT);
                // //
                // $class_no = T06tb::select('class')
                // ->where('date', '<=', $queryData['edate3'])
                // ->get();
                // $class_no_in = array();
                // foreach ($class_no as $row) {
                //     $class_no_in[] = $row->class;
                // }
                // $query->whereIn('t01tb.class', $class_no_in);
                // //
                $query->where('t06tb.date', '<=', $queryData['edate3']);
                //$query->distinct();
                $query->groupBy('t06tb.class');

            }
        }

        // 期別
        if ( isset($queryData['term']) && $queryData['term'] ) {

            // $queryData['term'] = str_pad($queryData['term'] ,2,'0',STR_PAD_LEFT);

            $query->where('t04tb.term', $queryData['term']);
        }


        $query->where(\DB::raw('substr(t01tb.class,1,3)'),'>=','105')->groupBy('t01tb.class','t01tb.name','t04tb.term','t54tb.times');
        //dd($result->toArray());



        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);
        for($i=0;$i<count($data);$i++){
            $data[$i]['sp_name']=$this->efs->getSponsor($data[$i]->sponsor);
        }
        //var_dump($data->toArray());
        //dd($data);
        return $data;
    }

    //當講座方面為空時 跑這個Function
    public function getTeacherAndCourse($class_info)
    {
        $query=T09tb::select("t09tb.class","t09tb.term","m01tb.cname",
        DB::raw('group_concat(course order by course) as course,group_concat(t09tb.idno order by course) as idno'));
        $query->join('m01tb',function($join){
            $join->on('m01tb.idno','=','t09tb.idno');
        });
        $query->where('class',$class_info['class']);
        $query->where('term',$class_info['term']);
        $data=$query->get();
        for($i=0;$i<count($data);$i++){
            $data[$i]['info']=$this->getClassAns($data[$i]);
            $data[$i]['class_name']=$this->getCourseName($data[$i]);
            $data[$i]['teacher_name']=$this->getTeacherName($data[$i]);
            $data[$i]['question']=$this->getQuestion($data[$i]);
        }
        return $data;
    }

    //講座方面
    public function getClass($queryData=[])
    {
        $query=T56tb::select('class','term','times',DB::raw('group_concat(course order by course) as course,group_concat(idno order by course) as idno,serno'));

        $query->where('t56tb.class','=',$queryData['class']);
        $query->where('t56tb.term','=',$queryData['term']);
        $query->where('t56tb.times','=',$queryData['times']);
        $query->groupBy('serno');
        //var_dump($queryData);
        //die();
        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 1);
        for($i=0;$i<count($data);$i++){
            $data[$i]['info']=$this->getClassAns($data[$i]);
            $data[$i]['class_name']=$this->getCourseName($data[$i]);
            $data[$i]['teacher_name']=$this->getTeacherName($data[$i]);
            $data[$i]['question']=$this->getQuestion($data[$i]);
        }
        //dd($data[0]['teacher_name'][0]->idno);
        return $data;
    }
    public function getQuestion($data)
    {
        $query=T95tb::select('q11','q12','q13','q14','q15','q21','q22','q23','q31','q32','q33','q41','q42','note');
        $query->where('class',$data->class);
        $query->where('term',$data->term);
        $query->where('times',$data->times);
        $query->where('serno',$data->serno);
        $info=$query->get();
        return $info;

    }
    //教學技法、教學內容、教學態度
    public function getClassAns($data)
    {
        $query=T56tb::select('t56tb.class','t56tb.course','ans1','ans2','ans3','fillmk');

        $query->where('t56tb.class',$data->class);
        $query->where('t56tb.term',$data->term);
        $query->where('t56tb.times',$data->times);
        $query->where('t56tb.serno',$data->serno);
        $info=$query->get();
        return $info;
    }
    //講座
    public function getTeacherName($data)
    {
        $query=M01tb::select('idno','cname');
        $idno=explode(",",$data->idno);
        $data=$query->whereIn('idno',$idno)->get();
        return $data;
    }
    //課程名稱
    public function getCourseName($data)
    {
        $query=T06tb::select('name');

        $query->where('class','=',$data->class);
        $query->where('term','=',$data->term);
        $course=explode(",",$data->course);
        $course_name=[];
        $course_name=$query->whereIn('course',$course)->get();
        return $course_name;
    }

    /**
     * 取得班別列表
     *
     * @return mixed
     */
    public function getClassList()
    {
        return DB::select('
             SELECT DISTINCT class,RTRIM(name) as name  FROM t01tb
             WHERE SUBSTRING(class,1,3) >= "105"
             AND EXISTS(SELECT * FROM t53tb WHERE class=t01tb.class  AND times != "" )
             ORDER BY class DESC
        ');
    }
    public function get_insert_t57tb_data($class_info)
    {
        $query=T95tb::select(DB::raw('ifnull((SELECT ifnull(AVG((CASE WHEN okrate=0 THEN NULL ELSE okrate END)),0)FROM t09tb
        WHERE EXISTS(SELECT * FROM t56tb WHERE class=t95tb.class AND term=t95tb.term AND times=t95tb.times
        AND class=t09tb.class AND term=t09tb.term AND course=t09tb.course AND idno=t09tb.idno)
        GROUP BY class,term ),0) AS teaper,
        (ifnull(AVG(CASE WHEN q11=0 THEN NULL ELSE q11*(20.00) END),0)+ifnull(AVG(CASE WHEN q12=0 THEN NULL ELSE q12*(20.00) END),0)
        +ifnull(AVG(CASE WHEN q13=0 THEN NULL ELSE q13*(20.00) END),0)
        +ifnull(AVG(CASE WHEN q14=0 THEN NULL ELSE q14*(20.00) END),0)
        +ifnull(AVG(CASE WHEN q15=0 THEN NULL ELSE q15*(20.00) END),0))/
        (CASE WHEN (SUM(q11)+SUM(q12)+SUM(q13)+SUM(q14)+SUM(q15))=0 THEN 1
        ELSE (
        (CASE WHEN SUM(q11)>0 THEN 1 ELSE 0 END)
        +(CASE WHEN SUM(q12)>0 THEN 1 ELSE 0 END)
        +(CASE WHEN SUM(q13)>0 THEN 1 ELSE 0 END)
        +(CASE WHEN SUM(q14)>0 THEN 1 ELSE 0 END)
        +(CASE WHEN SUM(q15)>0 THEN 1 ELSE 0 END))
        END )  AS conper,

        (ifnull(AVG(CASE WHEN q41=0 THEN NULL ELSE q41*(20.00) END),0)+ifnull(AVG(CASE WHEN q42=0 THEN NULL ELSE q42*(20.00) END),0))/(CASE WHEN (SUM(q41)+SUM(q42))=0 THEN 1 ELSE (CASE WHEN SUM(q41)>0 THEN 1 ELSE 0 END)+(CASE WHEN SUM(q42)>0 THEN 1 ELSE 0 END) END) AS envper,
        (ifnull(AVG(CASE WHEN q31=0 THEN NULL ELSE q31*(20.00) END),0)+ifnull(AVG(CASE WHEN q32=0 THEN NULL ELSE q32*(20.00) END),0)+ifnull(AVG(CASE WHEN q33=0 THEN NULL ELSE q33*(20.00) END),0))/(CASE WHEN (SUM(q31)+SUM(q32)+SUM(q33))=0 THEN 1 ELSE (CASE WHEN SUM(q31)>0 THEN 1 ELSE 0 END)+(CASE WHEN SUM(q32)>0 THEN 1 ELSE 0 END)+(CASE WHEN SUM(q33)>0 THEN 1 ELSE 0 END) END) AS worper,
        (ifnull(AVG(CASE WHEN q21=0 THEN NULL ELSE q21*(20.00) END),0)+ifnull(AVG(CASE WHEN q22=0 THEN NULL ELSE q22*(20.00) END),0)+ifnull(AVG(CASE WHEN q23=0 THEN NULL ELSE q23*(20.00) END),0))/(CASE WHEN (SUM(q21)+SUM(q22)+SUM(q23))=0 THEN 1 ELSE (CASE WHEN SUM(q21)>0 THEN 1 ELSE 0 END)+(CASE WHEN SUM(q22)>0 THEN 1 ELSE 0 END) +(CASE WHEN SUM(q23)>0 THEN 1 ELSE 0 END) END) AS attper,
        0 as totper'),'t95tb.class','t95tb.term','t95tb.times');
        if(isset($class_info['year'])){
            $query->where('t95tb.class','like',$class_info['year'].'%');
        }else{
            $query->where('t95tb.class',$class_info['class']);
            $query->where('t95tb.term',$class_info['term']);
            $query->where('t95tb.times',$class_info['times']);
        }

        $query->groupBy('t95tb.class','t95tb.term','t95tb.times');
        $data=$query->get();
        //dd($data);
        return $data;
    }
    public function get_update_t09tb($class_info)
    {

        $query=T09tb::select('*');
        $query->whereExists(function ($query) use ($class_info){
            $query->select(DB::raw('*'))->from('t56tb')
            ->whereRaw("class=t09tb.class and term=t09tb.term and course=t09tb.course and idno=t09tb.idno and
            class is not null");
            if(isset($class_info['year'])){
                $query->where('t56tb.class','like',$class_info['year'].'%');
            }else{
                $query->where('t56tb.class',$class_info['class']);
                $query->where('t56tb.term',$class_info['term']);
                $query->where('t56tb.times',$class_info['times']);
            }
        });

        $data=$query->get();
        return $data;
    }
    public function insertOkRate($class_info)
    {


        if(isset($class_info['year'])){
            $subquery="(SELECT class,term,course,idno,(ifnull(AVG(CASE WHEN ans1=0 THEN NULL ELSE ans1*(20.00) END),0)+ifnull(AVG(CASE WHEN ans2=0 THEN NULL ELSE ans2*(20.00) END),0)+ifnull(AVG(CASE WHEN ans3=0 THEN NULL ELSE ans3*(20.00) END),0))/3 AS okrate FROM t56tb
            WHERE  idno<>'' and class like ('{$class_info['year']}%')
            GROUP BY class,term,times,course,idno) B ";
        }else{
            $subquery="(SELECT class,term,course,idno,(ifnull(AVG(CASE WHEN ans1=0 THEN NULL ELSE ans1*(20.00) END),0)+ifnull(AVG(CASE WHEN ans2=0 THEN NULL ELSE ans2*(20.00) END),0)+ifnull(AVG(CASE WHEN ans3=0 THEN NULL ELSE ans3*(20.00) END),0))/3 AS okrate FROM t56tb
            WHERE  idno<>'' and class={$class_info['class']} and term={$class_info['term']} and times={$class_info['times']}
            GROUP BY class,term,times,course,idno) B ";
        }

        $query=T09tb::select('B.*');
        $query->join(DB::raw($subquery),function($join){
            $join->on('t09tb.class', '=', 'B.class');
            $join->on('t09tb.term','=','B.term');
            $join->on('t09tb.course','=','B.course');
            $join->on('t09tb.idno','=','B.idno');
        });

        $data=$query->get();
        return $data;
    }
}
