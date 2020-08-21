<?php
namespace App\Repositories;

use App\Models\DemandSurveyCommissioned;
use App\Models\DemandSurveyCommissionedPre;


class DemandSurveyCommissionedRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getDemandSurveyList($queryData = [])
    {

        $query = DemandSurveyCommissioned::select('id', 'yerly', 'item_id', 'sdate', 'edate');
        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            if (in_array($queryData['_sort_field'], ['demand_survey_id', 'year', 'item_id'])) {

                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        } else {
            // 預設排序
            $query->orderBy('id', 'desc');
        }

        // 年度
        if ( isset($queryData['yerly']) && $queryData['yerly'] ) {

            $queryData['yerly'] = str_pad($queryData['yerly'] ,3,'0',STR_PAD_LEFT);

            $query->where('yerly', $queryData['yerly']);
        }

        // 專碼
        if ( isset($queryData['item_id']) && $queryData['item_id'] ) {
            $query->where('item_id', $queryData['item_id']);
        }
     
        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);
       
        return $data;
    }

    /**
     * 取得專班列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getDemandSurveyPreList($queryData = [])
    {

  
        $query = DemandSurveyCommissionedPre::select('demand_survey_commissioned_pre.id','demand_survey_commissioned_pre.yerly','demand_survey_commissioned_pre.item_id','demand_survey_commissioned_pre.class_name','demand_survey_commissioned_pre.object','demand_survey_commissioned_pre.target', 'periods','demand_survey_commissioned_pre.periods_people','demand_survey_commissioned_pre.training_days','demand_survey_commissioned_pre.entrusting_orga',
        'entrusting_unit','demand_survey_commissioned_pre.entrusting_contact','demand_survey_commissioned_pre.phone','demand_survey_commissioned_pre.email','demand_survey_commissioned_pre.classroom_type','demand_survey_commissioned_pre.remarks','demand_survey_commissioned_pre.audit_status','demand_survey_commissioned_pre.enable');
    
        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            if (in_array($queryData['_sort_field'], [ 'yerly', 'item_id'])) {

                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        } else {
            // 預設排序
            $query->orderBy('id', 'desc');
        }

        // 年度
        if ( isset($queryData['yerly']) && $queryData['yerly'] ) {

            $queryData['yerly'] = str_pad($queryData['yerly'] ,3,'0',STR_PAD_LEFT);

            $query->where('yerly', $queryData['yerly']);
        }
        // 專碼
        if ( isset($queryData['item_id']) && $queryData['item_id'] ) {
            $query->where('item_id', $queryData['item_id']);
        }
        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;

    }

    
     /**
     * 取得審核中與審核通過的專班列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getDemandSurveyAuditList($queryData = [])
    {
   
        $query = DemandSurveyCommissionedPre::select('id','yerly','item_id','class_name','object','target', 'periods','periods_people','training_days','sdate','edate','entrusting_orga',
        'entrusting_unit','entrusting_contact','phone','email','classroom_type','remarks','audit_status','enable');

  
        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            if (in_array($queryData['_sort_field'], [ 'yerly', 'item_id'])) {

                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        } else {
            // 預設排序
            $query->orderBy('id', 'desc');
        }

        // 年度
        if ( isset($queryData['yerly']) && $queryData['yerly'] ) {

            $queryData['yerly'] = str_pad($queryData['yerly'] ,3,'0',STR_PAD_LEFT);

            $query->where('yerly', $queryData['yerly']);
        }
        // 專碼
        if ( isset($queryData['item_id']) && $queryData['item_id'] ) {
            $query->where('item_id', $queryData['item_id']);
        }

        //狀態
        $query->whereIn('audit_status', array('審核中', '審核通過'));
        $data = $query->get()->toArray();
        return $data;

    }
    
}
