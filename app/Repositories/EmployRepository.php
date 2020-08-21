<?php
namespace App\Repositories;

use App\Models\T09tb;
use App\Models\T08tb;
use App\Models\T06tb;
use App\Models\T01tb;
use App\Models\T04tb;
use App\Models\M09tb;
use App\Models\M01tb;
use App\Models\Employ_sort;
use DB;


class EmployRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getEmployList($queryData = [])
    {

        $query = T01tb::select('t04tb.sponsor', 't04tb.sdate', 't04tb.edate', 't04tb.term', 't01tb.class', 't01tb.name', 't01tb.branch' , 't01tb.process' , 't01tb.branchname');

        $query->join('t04tb', function($join)
        {
            $join->on('t04tb.class', '=', 't01tb.class');
        });

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
            $queryData['yerly'] = str_pad($queryData['yerly'],3,'0',STR_PAD_LEFT);
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

        if ( isset($queryData['class_branch_name']) && $queryData['class_branch_name'] ) {
            $query->where('t01tb.branchname', 'LIKE', '%'.$queryData['class_branch_name'].'%');
        }

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
            $query->where('t01tb.type', 'LIKE', '%'.$queryData['type'].'%');
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

            // //
            // $class_no = T06tb::select('class')
            // ->where('date', '>=', $queryData['sdate3'])
            // ->where('date', '<=', $queryData['edate3'])
            // ->get();
            // $class_no_in = array();
            // foreach ($class_no as $row) {
            //     $class_no_in[] = $row->class;
            // }
            // $query->whereIn('t01tb.class', $class_no_in);
            // //
            $query->where('t06tb.date', '>=', $queryData['sdate3']);
            $query->where('t06tb.date', '<=', $queryData['edate3']);
            $query->distinct();
        }else{
            if ( isset($queryData['sdate3']) && $queryData['sdate3'] ) {
                $query->leftJoin('t06tb', function($join)
                {
                    $join->on('t04tb.class', '=', 't06tb.class')
                    ->on('t04tb.term', '=', 't06tb.term');
                });
                $queryData['sdate3'] = str_pad($queryData['sdate3'],7,'0',STR_PAD_LEFT);
                // //
                // $class_no = T06tb::select('class')
                // ->where('date', '>=', $queryData['sdate3'])
                // ->get();
                // $class_no_in = array();
                // foreach ($class_no as $row) {
                //     $class_no_in[] = $row->class;
                // }
                // $query->whereIn('t01tb.class', $class_no_in);
                // //
                $query->where('t06tb.date', '>=', $queryData['sdate3']);
                $query->distinct();
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
                $query->distinct();
            }
        }

        // 期別
        if ( isset($queryData['term']) && $queryData['term'] ) {

            // $queryData['term'] = str_pad($queryData['term'] ,2,'0',STR_PAD_LEFT);

            $query->where('t04tb.term', $queryData['term']);
        }

        $query->groupBy('t04tb.class', 't04tb.term');

        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }

    public function getSponsor(){
        $query = T04tb::select('t04tb.sponsor', 'm09tb.username');
        $query->join('m09tb', function($join)
        {
            $join->on('m09tb.userid', '=', 't04tb.sponsor');
        });
        $results = $query->where('sponsor', '<>', '')->distinct()->get()->toArray();
        $sponsor = array();
        foreach($results as $row){
            $sponsor[$row['sponsor']] = $row['username'];
        }

        $sponsor[''] = '';

        return $sponsor;
    }

    public function getClass($queryData = [])
    {
        $query = T01tb::select('t01tb.class', 't01tb.name', 't01tb.branch', 't01tb.branchname', 't01tb.process');
        $results = $query->where('class', $queryData['class'])->get()->toArray();
        $class_data = $results[0];
        $query = T04tb::select('t04tb.term', 't04tb.sdate', 't04tb.edate', 't04tb.sponsor');
        $results = $query->where('class', $queryData['class'])->where('term', $queryData['term'])->get()->toArray();
        $class_data['term'] = $results[0]['term'];
        $class_data['sdate'] = $results[0]['sdate'];
        $class_data['edate'] = $results[0]['edate'];
        $class_data['sponsor'] = $results[0]['sponsor'];
        if(!empty($class_data['sponsor'])){
            $query = M09tb::select('m09tb.username');
            $results = $query->where('userid', $class_data['sponsor'])->get()->toArray();
            $class_data['sponsor'] = $results[0]['username'];
            //123
        }
        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($class_data);
        // echo "\n</pre>\n";
        // die();

        return $class_data;
    }

    public function getDetailList($queryData = [])
    {
        $query = T09tb::select('t09tb.id', 't09tb.course', 't09tb.idno', 't09tb.type', 't09tb.lecthr', 't09tb.lectamt', 't09tb.teachtot', 't09tb.tratot', 't09tb.deductamt', 'totalpay');
        $list = $query->where('class', $queryData['class'])->where('term', $queryData['term'])->get()->toArray();

        foreach($list as $key => $row){
            $query = T06tb::select('t06tb.name', 't06tb.date');
            $class_name = $query->where('class', $queryData['class'])->where('term', $queryData['term'])->where('course', $row['course'])->get()->toArray();

            if(isset($class_name[0]['name'])){
                $list[$key]['name'] = $class_name[0]['name'];
            }else{
                $list[$key]['name'] = '';
            }

            if(isset($class_name[0]['date'])){
                $list[$key]['date'] = substr($class_name[0]['date'], '3', '2').'/'.substr($class_name[0]['date'], '5', '2');
            }else{
                $list[$key]['date'] = '';
            }

            $query = T08tb::select('t08tb.cname');
            $t08data = $query->where('class', $queryData['class'])->where('term', $queryData['term'])->where('course', $row['course'])->where('idno', $row['idno'])->get()->toArray();

            if(isset($t08data[0]['cname'])){
                $list[$key]['cname'] = $t08data[0]['cname'];
            }else{
                $query = M01tb::select('m01tb.cname');
                $m01data = $query->where('idno', $row['idno'])->get()->toArray();
                $list[$key]['cname'] = $m01data[0]['cname'];
            }

            // echo '<pre style="text-align:left;">' . "\n";
            // print_r($t09data);
            // echo "\n</pre>\n";


        }

        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($list);
        // echo "\n</pre>\n";
        // die();
        return $list;
    }

    public function getEditDelete($id=NULL)
    {
        $EditDelete = array(
            'EditorDelete' => 'N',
            'paidday' => '',
        );

        $query = T09tb::select('*');
        $data = $query->where('id', $id)->get()->toArray();
        $data = $data[0];

        $paidday = $data['paidday'];

        if(empty($data['paidday'])){
            $EditDelete['EditorDelete'] = 'Y';
        }else{
            if($data['paidday'] == ''){
                $EditDelete['EditorDelete'] = 'Y';
            }else{
                $EditDelete['paidday'] = $data['paidday'];
            }
        }

        return $EditDelete;
    }

    public function getTeacher($queryData = []){
        $query = T09tb::select('t09tb.idno', 'm01tb.cname', 'employ_sort.teacher_sort');
        $query->join('m01tb', function($join)
        {
            $join->on('m01tb.idno', '=', 't09tb.idno');
        });
        $query->leftjoin('employ_sort', function($join)
        {
            $join->on('employ_sort.class', '=', 't09tb.class')
            ->on('employ_sort.term', '=', 't09tb.term')
            ->on('employ_sort.idno', '=', 't09tb.idno');
        });
        $query->where('t09tb.class', $queryData['class'])->where('t09tb.term', $queryData['term']);
        $query->orderBy(DB::raw('ISNULL(teacher_sort), teacher_sort'));
        $query->groupBy('t09tb.idno');
        $results = $query->get()->toArray();

        return $results;
    }

}
