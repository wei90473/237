<?php
namespace App\Repositories;

use App\Models\T01tb;
use App\Models\T04tb;
use App\Models\M09tb;
use App\Models\T13tb;
use App\Models\Class_mail;
use App\Models\TTL_mail;

class NoticeEmailRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getNoticeEmailList($queryData = [])
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


        // 期別
        if ( isset($queryData['term']) && $queryData['term'] ) {

            // $queryData['term'] = str_pad($queryData['term'] ,2,'0',STR_PAD_LEFT);

            $query->where('t04tb.term', $queryData['term']);
        }

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
        $query = T01tb::select('t01tb.class', 't01tb.name', 't01tb.branch', 't01tb.branchname', 't01tb.process' , 't01tb.commission');
        $results = $query->where('class', $queryData['class'])->get()->toArray();

        //增加null判斷
        $class_data = isset($results[0])?$results[0]:[];
        // 原始碼
        // $class_data = $results[0];

        $query = T04tb::select('t04tb.term', 't04tb.sdate', 't04tb.edate', 't04tb.sponsor');
        $results = $query->where('class', $queryData['class'])->where('term', $queryData['term'])->get()->toArray();
        //增加null判斷
        $class_data['term'] = isset($results[0]['term'])?$results[0]['term']:"";
        $class_data['sdate'] = isset($results[0]['sdate'])?$results[0]['sdate']:"";
        $class_data['edate'] = isset($results[0]['edate'])?$results[0]['edate']:"";
        $class_data['sponsor'] = isset($results[0]['sponsor'])?$results[0]['sponsor']:"";
        // 原始碼
        // $class_data['term'] = $results[0]['term'];
        // $class_data['sdate'] = $results[0]['sdate'];
        // $class_data['edate'] = $results[0]['edate'];
        // $class_data['sponsor'] = $results[0]['sponsor'];

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

    public function getMailData($queryData = [])
    {
        $query = Class_mail::select('class_mail.class', 'class_mail.term', 'class_mail.title', 'class_mail.mail_list', 'class_mail.content' , 'class_mail.date');
        $results = $query->where('class', $queryData['class'])->where('term', $queryData['term'])->get()->toArray();
        if(!empty($results)){
            $results = $results[0];
        }
        return $results;
    }

    public function getStudentMail($queryData = [])
    {
        $query = T13tb::select('t13tb.*', 'm02tb.email', 'm02tb.cname');
        $query->leftJoin('m02tb', 'm02tb.idno', '=', 't13tb.idno');
        $results = $query->where('class', $queryData['class'])->where('term', $queryData['term'])->get()->toArray();

        $query = Class_mail::select('class_mail.class', 'class_mail.term', 'class_mail.title', 'class_mail.mail_list', 'class_mail.content' , 'class_mail.date');
        $mail_data = $query->where('class', $queryData['class'])->where('term', $queryData['term'])->get()->toArray();
        $mail_list = array();
        if(!empty($mail_data) && !empty($mail_data[0]['mail_list'])){
            $mail_list = explode(",",$mail_data[0]['mail_list']);
        }
        $data = array();
        foreach($results as $key => $row){
            $data[$key] = $row;
            $data[$key]['mail_list'] = 'N';
            if(!empty($mail_list)){
                if(in_array($row['email'], $mail_list)){
                    $data[$key]['mail_list'] = 'Y';
                }
            }else{
                $data[$key]['mail_list'] = 'Y';
            }
        }

        return $data;
    }

    public function getTTLMailData($queryData = [])
    {
        $query = TTL_mail::select('TTL_mail.class', 'TTL_mail.term', 'TTL_mail.subject', 'TTL_mail.mail_list', 'TTL_mail.editor' , 'TTL_mail.date');
        $results = $query->where('class', $queryData['class'])->where('term', $queryData['term'])->get()->toArray();
        if(!empty($results)){
            $results = $results[0];
        }
        return $results;
    }

    public function getTTLStudentMail($queryData = [])
    {
        $query = T13tb::select('t13tb.*', 'm02tb.email', 'm02tb.cname');
        $query->leftJoin('m02tb', 'm02tb.idno', '=', 't13tb.idno');
        $results = $query->where('class', $queryData['class'])->where('term', $queryData['term'])->get()->toArray();

        $query = TTL_mail::select('TTL_mail.class', 'TTL_mail.term', 'TTL_mail.subject', 'TTL_mail.mail_list', 'TTL_mail.editor' , 'TTL_mail.date');
        $mail_data = $query->where('class', $queryData['class'])->where('term', $queryData['term'])->get()->toArray();
        $mail_list = array();
        if(!empty($mail_data) && !empty($mail_data[0]['mail_list'])){
            $mail_list = explode(",",$mail_data[0]['mail_list']);
        }
        $data = array();
        foreach($results as $key => $row){
            $data[$key] = $row;
            $data[$key]['mail_list'] = 'N';
            if(!empty($mail_list)){
                if(in_array($row['email'], $mail_list)){
                    $data[$key]['mail_list'] = 'Y';
                }
            }else{
                $data[$key]['mail_list'] = 'Y';
            }
        }
        return $data;
    }

}
