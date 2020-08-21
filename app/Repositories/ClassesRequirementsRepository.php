<?php
namespace App\Repositories;

use App\Models\T01tb;
use App\Models\T04tb;
use App\Models\T06tb;
use App\Models\S02tb;
use App\Models\T23tb;
use App\Models\T13tb;
use App\Helpers\ModifyLog;
use Auth;
use DB;


class ClassesRequirementsRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getClassesRequirementsList($queryData = [])
    {
        // 取得登入使用者
        $uesr = Auth::guard('managers')->user()->userid;

        $query = T01tb::select('t01tb.class','t01tb.branch','t01tb.name','t01tb.branchname','t01tb.branchcode','t04tb.term','t01tb.process','t01tb.commission','t01tb.teaching','t04tb.sdate','t04tb.edate','t04tb.sponsor','m09tb.username');
        $query->join('t04tb', 't01tb.class', '=', 't04tb.class', 'INNER');
        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {
            if (in_array($queryData['_sort_field'], ['class'])) {
                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        }else {
            // 預設排序
            $query->orderBy('class', 'desc')->orderBy('term');
        }

        // 班號
        if ( isset($queryData['class']) && $queryData['class'] ) {
            $query->where('t01tb.class', 'like', '%'.$queryData['class'].'%');
        }
        // 班別名稱
        if ( isset($queryData['name']) && $queryData['name'] ) {
            $query->where('t01tb.name', 'like', '%'.$queryData['name'].'%');
        }
        // 期別
        if ( isset($queryData['term']) && $queryData['term'] ) {
            $query->where('t04tb.term', 'like', '%'.$queryData['term']);
        }
        // 分班名稱
        if ( isset($queryData['branchname']) && $queryData['branchname'] ) {
            $query->where('t01tb.branchname', 'like', '%'.$queryData['branchname'].'%');

        }
        // 年度
        if ( isset($queryData['yerly']) && $queryData['yerly'] ) {
            $queryData['yerly'] = str_pad($queryData['yerly'],3,'0',STR_PAD_LEFT);
            $query->where('t01tb.yerly', $queryData['yerly']);
        }
        // 辦班院區
        if ( isset($queryData['branch']) && $queryData['branch'] ) {

            $query->where('t01tb.branch', $queryData['branch']);
        }
        // 班別類型
        if ( isset($queryData['process']) && $queryData['process'] ) {

            $query->where('t01tb.process', $queryData['process']);
        }
        // 班務人員
        if ( isset($queryData['sponsor']) && $queryData['sponsor'] ) {
            $query->where('t04tb.sponsor', $queryData['sponsor']);
        }
        // 委訓機關
        if ( isset($queryData['commission']) && $queryData['commission'] ) {
            $query->where('t01tb.commission', $queryData['commission']);
        }
        // 訓練性質
        if ( isset($queryData['traintype']) && $queryData['traintype'] ) {
            $query->where('t01tb.traintype', $queryData['traintype']);
        }
        // 班別性質
        if ( isset($queryData['type']) && $queryData['type'] ) {
            $query->where('t01tb.type', $queryData['type']);
        }
        // 類別1
        if ( isset($queryData['categoryone']) && $queryData['categoryone'] ) {
            $query->where('t01tb.categoryone', $queryData['categoryone']);
        }
        // 開訓日期
        if ( isset($queryData['sdate']) && $queryData['sdate'] ) {
            $queryData['sdate'] = str_pad($queryData['sdate'],7,'0',STR_PAD_LEFT);
            $query->where('t04tb.sdate', '>=', $queryData['sdate']);
        }
        if ( isset($queryData['edate']) && $queryData['edate'] ) {
            $queryData['edate'] = str_pad($queryData['edate'],7,'0',STR_PAD_LEFT);
            $query->where('t04tb.sdate', '<=', $queryData['edate']);
        }
        // 結訓日期
        if ( isset($queryData['sdate2']) && $queryData['sdate2'] ) {
            $queryData['sdate2'] = str_pad($queryData['sdate2'],7,'0',STR_PAD_LEFT);
            $query->where('t04tb.edate', '>=', $queryData['sdate2']);
        }
        if ( isset($queryData['edate2']) && $queryData['edate2'] ) {
            $queryData['edate2'] = str_pad($queryData['edate2'],7,'0',STR_PAD_LEFT);
            $query->where('t04tb.edate', '<=', $queryData['edate2']);
        }
        // 在訓日期
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
            $query->distinct();
            $query->groupBy('t04tb.class', 't04tb.term');
        }else{
            if ( isset($queryData['sdate3']) && $queryData['sdate3'] ) {
                $query->leftJoin('t06tb', function($join)
                {
                    $join->on('t04tb.class', '=', 't06tb.class')
                    ->on('t04tb.term', '=', 't06tb.term');
                });
                $queryData['sdate3'] = str_pad($queryData['sdate3'],7,'0',STR_PAD_LEFT);
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
                $query->where('t06tb.date', '<=', $queryData['edate3']);
                $query->distinct();
            }
            $query->groupBy('t04tb.class', 't04tb.term');
        }
        if ( isset($queryData['group']) && $queryData['group'] ) {
            $query->groupby($queryData['group']);
        }
        $query->leftjoin('m09tb','t04tb.sponsor','=','m09tb.userid');
        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;

    }

    // 更新單價
    public function updataunitprice($queryData=[]){
        if ( !isset($queryData['sdate'])  ) {
            $queryData['sdate'] = (date('Y')-1911).(date('md'));
        }
        if ( !isset($queryData['edate'])  ) {
            $queryData['edate'] = (date('Y')-1911).(date('md'));
        }

        $base  = S02tb::select('sinunit','doneunit','dtwounit','meaunit','lununit','dinunit','buffer1','buffer2')->whereRaw('1 = 1')->first();
        $data = array();
        $data['teaunit']  = $base['teaunit']  ;
        $data['meaunit']  = $base['meaunit']  ;
        $data['lununit']  = $base['lununit']  ;
        $data['dinunit']  = $base['dinunit']  ;
        $data['sinunit']  = $base['sinunit']  ;
        $data['doneunit'] = $base['doneunit'] ;
        $data['dtwounit'] = $base['dtwounit'] ;
        $data['meaunit']  = $base['meaunit']  ;
        $data['bufunit']  = DB::raw( "CASE WHEN buftype = '1' THEN  ".$base['buffer1']." WHEN buftype = '2' THEN  ".$base['buffer2']." ELSE '0' END");
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
        $old = T23tb::whereBetween('date',array($queryData['sdate'],$queryData['edate']))->get()->toarray();
        $query = T23tb::whereBetween('date',array($queryData['sdate'],$queryData['edate']))->update($data);
        $sql = DB::getQueryLog();
        $now = T23tb::whereBetween('date',array($queryData['sdate'],$queryData['edate']))->get()->toarray();
        createModifyLog('U','t23tb',$old,$now,end($sql));
        return $query;

    }
    // 編輯頁
    public function getEditList($queryData = [])
    {
        // 取得登入使用者
        $uesr = Auth::guard('managers')->user()->userid;
        $query = T01tb::select('t01tb.class','t01tb.branch','t01tb.name','t01tb.branchname','t01tb.branchcode','t04tb.term','t01tb.process','t01tb.commission','t04tb.sdate','t04tb.edate','t04tb.time','t04tb.sponsor','m09tb.username','t04tb.kind');
        $query->join('t04tb', 't01tb.class', '=', 't04tb.class', 'INNER');

        // 班號
        if ( isset($queryData['class']) && $queryData['class'] ) {
            $query->where('t01tb.class', 'like', '%'.$queryData['class'].'%');
        }
        // 班別名稱
        if ( isset($queryData['name']) && $queryData['name'] ) {
            $query->where('t01tb.name', 'like', '%'.$queryData['name'].'%');
        }
        // 期別
        if ( isset($queryData['term']) && $queryData['term'] ) {
            $query->where('t04tb.term', 'like', '%'.$queryData['term']);
        }

        $query->leftjoin('m09tb','t04tb.sponsor','=','m09tb.userid');
        $data = $query->first();

        return $data;

    }
    // 計算出膳宿數量
    public function getLiveList($queryData = [] ){
        if(!isset($queryData['groupterm']) || !isset($queryData['groupclass'])){
            return 0;
        }
        $query = T13tb::select(DB::raw("sum(if(extradorm='Y','1','0'))as extradorm,sum(if(vegan='Y',if(extradorm='Y','1','0'),'0'))as extradorm_vegan,sum(if(vegan='Y',if(dorm='Y','1','0'),'0'))as dorm_vegan,sum(if(vegan='Y','1','0'))as vegan,count(*)+2 as luncnt,sum(if(dorm='Y','1','0'))as dincnt"));
        $data = $query->where('class', 'like', '%'.$queryData['groupclass'].'%')->where('term', $queryData['groupterm'])->where('status','1')->first();
        return $data;
    }

    // 伙食費核銷總表
    public function getFoodExpenseList($queryData = [] ){
        if (isset($queryData['yerly']) && isset($queryData['month'])) {
            $yyy = str_pad($queryData['yerly'],3,'0',STR_PAD_LEFT);
            $mm = str_pad($queryData['month'],2,'0',STR_PAD_LEFT);
        }else{
            return 0;
        }
        $query = T04tb::select('t01tb.class','t01tb.branch','t01tb.name','t01tb.process','t01tb.commission','m17tb.enrollname','t04tb.term','t04tb.sdate','t04tb.edate','t04tb.section','t04tb.kind','s06tb.accname');
        $query->Join('t01tb', function($join)
            {
                $join->on('t04tb.class', '=', 't01tb.class')
                ->on('t01tb.branch', '=',DB::RAW('2'));
            });
        $query->Join('s06tb', DB::RAW(" t04tb.kind = s06tb.acccode AND s06tb.yerly"),DB::RAW($queryData['yerly'])) ;
        $query->leftJoin('m17tb','t01tb.commission','m17tb.enrollorg');
        $query->whereBetween('t04tb.sdate', array($yyy.$mm.'00',$yyy.$mm.'32'));
        $query->orwhereBetween('t04tb.edate', array($yyy.$mm.'00',$yyy.$mm.'32'));

        $data = $query->get()->toarray();

        return $data;
    }
}
