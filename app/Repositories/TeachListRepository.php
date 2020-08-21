<?php
namespace App\Repositories;

use App\Models\T13tb;
use App\Models\T01tb;
use App\Models\T49tb;
use App\Models\S06tb;
use Auth;
use DB;


class TeachListRepository
{
    /**
     * 取得班別列表
     *
     * @return mixed
     */
    public function getClassList($queryData = [])
    {
        // 取得登入使用者
        $uesr = Auth::guard('managers')->user()->userid;

        $query = T01tb::select('t01tb.class','t01tb.branch','t01tb.name','t01tb.branchname','t04tb.term','t01tb.process','t01tb.commission','t01tb.teaching','t04tb.sdate','t04tb.edate','t04tb.sponsor','m09tb.username');
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
        //不要列洽借班期
        if (isset($queryData['type13'])){
            $query->where('t01tb.type', '<>','13');
        }

        if ( isset($queryData['teaching']) && $queryData['teaching'] ) {
            $query->where('t01tb.teaching', $queryData['teaching']);
        }
        // 班號
        if ( isset($queryData['class']) && $queryData['class'] ) {
            $query->where('t01tb.class', 'like', '%'.$queryData['class'].'%');
        }
        // 班別名稱
        if ( isset($queryData['name']) && $queryData['name'] ) {
            $query->where('t01tb.name', 'like', '%'.$queryData['name'].'%');
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
            $query->where('t01tb.process', 'LIKE', '%'.$queryData['process'].'%');
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
        // 上課地點
        if ( isset($queryData['sitebranch']) && $queryData['sitebranch'] ) {
            $query->where('t04tb.site_branch', $queryData['sitebranch']);
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
            // $query->groupBy('t04tb.class', 't04tb.term');
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
            // $query->groupBy('t04tb.class', 't04tb.term');
        }
        $query->leftjoin('m09tb','t04tb.sponsor','=','m09tb.userid');
        $query->groupby('t01tb.class');
    
        
        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;

    }

    }
