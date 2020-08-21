<?php
namespace App\Repositories;

use App\Models\T53tb;
use App\Models\T01tb;
use App\Models\T04tb;
use App\Models\T95tb;

class EffectivenessSurveyRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    /*public function getEffectivenessSurveyList($queryData = [])
    {
        $query = T53tb::select('id', 'class', 'class', 'term', 'times');

        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            if (in_array($queryData['_sort_field'], ['class', 'class', 'term', 'times'])) {

                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        } else {
            // 預設排序
            $query->orderBy('class', 'desc');
        }

        // 一定要有搜尋
        if ($queryData['class'] && $queryData['term'] && $queryData['times']) {

            $query->where('class', $queryData['class']);

            $query->where('term', $queryData['term']);

            $query->where('times', $queryData['times']);
        } else {
            $query->where('class', 'N');
        }

        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }*/

    public function getEffectivenessSurveyList($queryData = [],$mode=null)
    {
        if($mode=='edit'){
            $query = T53tb::select('t53tb.class', 'copy', 't53tb.term', 't53tb.times','t01tb.branch','t01tb.name','t01tb.branchname',
                                't01tb.traintype','t01tb.process','t04tb.sponsor','t04tb.sdate','t04tb.edate','t04tb.client','t53tb.fillsdate','t53tb.filledate'
                                ,'t01tb.commission');
            $query->join('t04tb',function($join)
            {
                $join->on('t04tb.class','=','t53tb.class');
                $join->on('t04tb.term','=','t53tb.term');
            });
            $query->join('t01tb',function($join)
            {
                $join->on('t01tb.class','=','t53tb.class');
                 //->on('t01tb.times','=','t53tb.times');
            });
        }else{
            $query = T04tb::select('t04tb.class','t04tb.term','t01tb.branch','t01tb.name','t01tb.branchname','t01tb.traintype','t01tb.process','t04tb.sponsor','t04tb.sdate','t04tb.edate','t04tb.client');
            $query->join('t01tb',function($join)
            {
                $join->on('t01tb.class','=','t04tb.class');
                 //->on('t01tb.times','=','t53tb.times');
            });
        }









        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            if (in_array($queryData['_sort_field'], [])) {

                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        } else {
            // 預設排序
            $query->orderBy('class', 'desc');
            $query->orderBy('term', 'asc');
        }

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

        if ( isset($queryData['process']) && $queryData['process'] ) {
            $query->where('t01tb.process', 'LIKE', '%'.$queryData['process'].'%');
        }

        if ( isset($queryData['traintype']) && $queryData['traintype'] ) {
            $query->where('t01tb.traintype', 'LIKE', '%'.$queryData['traintype'].'%');
        }

        if ( isset($queryData['categoryone']) && $queryData['categoryone'] ) {
            $query->where('t01tb.categoryone', $queryData['categoryone']);
        }

        if ( isset($queryData['sponsor']) && $queryData['sponsor'] ) {
            $query->where('t04tb.sponsor', '=', $queryData['sponsor']);
        }

        if ( isset($queryData['type']) && $queryData['type'] ) {
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
        if($mode=='edit'){
            $query->where('t53tb.times',$queryData['times']);
        }

        /*if($mode=='list'){
            $query->groupBy('t53tb.class');
            $query->groupBy('t53tb.term');
        }*/

        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);
        //取得sponsor中文名字
        for($i=0;$i<count($data);$i++){
            $data[$i]['sp_name']=$this->getSponsor($data[$i]->sponsor);
        }
        //dd($data);
        return $data;

    }
    public function getSponsor($sponsor=null)
    {
        $index=$sponsor;
        $query = T04tb::select('t04tb.sponsor','m09tb.username');
        $query->join('m09tb', function($join)
        {
            $join->on('m09tb.userid', '=', 't04tb.sponsor');
        });
        if(isset($sponsor)){
            $query->where('t04tb.sponsor','=',$sponsor);
        }
        $results = $query->where('sponsor', '<>', '')->distinct()->get()->toArray();
        $sponsor = array();
        foreach($results as $row){
            $sponsor[$row['sponsor']] = $row['username'];
        }
        //dd($index);
        if($index!=''){
            return $sponsor[$index];
        }
        return null;
    }
    public function getT53tb($data=[],$class_info)
    {
        $query=T53tb::select('*');
        $query->where('t53tb.class',$class_info['class']);
        $query->where('t53tb.term',$class_info['term']);
        $query->where('t53tb.times',$class_info['times']);
        $query->update($data);
    }
    public function getClassTimes($class_info)
    {
        $query = T53tb::select('t53tb.class', 'copy', 't53tb.term', 't53tb.times','t01tb.branch','t01tb.name','t01tb.branchname',
        't01tb.traintype','t01tb.process','t04tb.sponsor','t04tb.sdate','t04tb.edate','t04tb.client','t53tb.fillsdate','t53tb.filledate');

        $query->where('t53tb.class',$class_info['class']);
        $query->where('t53tb.term',$class_info['term']);

        $query->join('t04tb',function($join)
        {
            $join->on('t04tb.class','=','t53tb.class')
                 ->on('t04tb.term','=','t53tb.term');
        });

        $query->join('t01tb',function($join)
        {
            $join->on('t01tb.class','=','t53tb.class');
                 //->on('t01tb.times','=','t53tb.times');
        });
        $results = $query->get();

        for($i=0;$i<count($results);$i++){
            $results[$i]['sp_name']=$this->getSponsor($results[$i]->sponsor);
        }

        return $results;
    }
    public function getAns($class_info)
    {
        $query=T95tb::select('*');
        $data=$query->where('class',$class_info['class'])->where('term',$class_info['term'])->where('times',$class_info['times'])->get();
        return $data->toArray();
    }
}
