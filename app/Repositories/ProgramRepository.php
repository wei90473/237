<?php
namespace App\Repositories;

use App\Models\M11tb;
use App\Models\T35tb;
use DB ;

class ProgramRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getProgramList($queryData = [])
    {
        $query = M11tb::select('progid', 'progname', 'logmk');

        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            if (in_array($queryData['_sort_field'], ['progid', 'progname', 'logmk'])) {

                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        } else {
            // 預設排序
            // $query->orderBy('progid', 'desc');
        }

        // 程式代號
        if ( isset($queryData['progid']) && $queryData['progid']) {

            $query->where('progid', 'LIKE', '%'.$queryData['progid'].'%');
        }

        // 程式名稱
        if ( isset($queryData['progname']) && $queryData['progname']) {

            $query->where('progname', 'LIKE', '%'.$queryData['progname'].'%');
        }

        $data = $query->paginate($queryData['_paginate_qty']);

        return $data;
    }

    /**
     * 取得查詢列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getSearchList($queryData = [])
    {
        $query = T35tb::select(DB::raw("RTRIM(IFNULL(m09tb.username,t35tb.userid)) AS username,RTRIM(m11tb.progname) AS progname,
       CONCAT(SUBSTRING(t35tb.logdate,1,3) ,'/',SUBSTRING(t35tb.logdate,4,2),'/',SUBSTRING(t35tb.logdate,6,2)) AS date,
       t35tb.logtime AS logtime,
       CASE t35tb.type
        WHEN 'R' THEN '查詢'
        WHEN 'I' THEN '新增'
        WHEN 'U' THEN '修改'
        WHEN 'D' THEN '刪除'
        WHEN 'B' THEN '批次作業'
        ELSE ''  END AS type,
       LOWER(t35tb.logtable) AS logtable, t35tb.content, t35tb.userid "));
        $query->join('m11tb','m11tb.progid','t35tb.progid');
        $query->join('m09tb','m09tb.userid','t35tb.userid');
        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');

        } else {
            // 預設排序
            $query->orderBy('t35tb.logdate', 'desc');
        }

        // 程式代號
        if ( isset($queryData['progid']) && $queryData['progid']) {

            $query->where('t35tb.progid',$queryData['progid']);
        }

        // 使用者帳號
        if ( isset($queryData['userid']) && $queryData['userid']) {

            $query->where('t35tb.userid',$queryData['userid']);
        }

        // 開始時間
        if ( isset($queryData['logsdate']) && $queryData['logsdate'] ) {
            $query->where('t35tb.logdate','>', $queryData['logsdate']);
        }

        // 結束時間
        if ( isset($queryData['logedate']) && $queryData['logedate'] ) {
            $query->where('t35tb.logdate','<', $queryData['logedate']);
        }
        // $query->where('m11tb.logmk','Y');
        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 20);

        return $data;
    }
}
