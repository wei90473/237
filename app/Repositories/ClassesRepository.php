<?php
namespace App\Repositories;

use App\Models\T01tb;
use App\Models\T04tb;
use App\Models\T08tb;
use App\Models\T97tb;
use App\Models\S01tb;
use App\Models\Edu_loanplacelst;

use DB;

class ClassesRepository
{
    /**
     * 取得列表
     *
     * @param array $queryData 關鍵字
     * @return mixed
     */
    public function getClassesList($queryData = [])
    {
        $query = T01tb::select('class', 'name','branchcode', 'style', 'board', 'period', 'kind','branch','type','process','rank','time1','time2','time3','time4','time5','time6','time7','holiday','upload1','profchk','trace');

        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            if (in_array($queryData['_sort_field'], ['class'])) {

                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        } else {
            // 預設排序
            $query->orderBy(DB::raw('rank,class'));
        }

        // 班號
        if ( isset($queryData['class']) && $queryData['class'] ) {
            
            $query->where('class', 'like', '%'.$queryData['class'].'%');
        }
        // 班別名稱
        if ( isset($queryData['name']) && $queryData['name'] ) {
            
            $query->where('name', 'like', '%'.$queryData['name'].'%');
        }
        // 分班名稱
        if ( isset($queryData['branchname']) && $queryData['branchname'] ) {
            $query->where('branchname', 'like', '%'.$queryData['branchname'].'%');

        }    
        // 訓練性質
        if ( isset($queryData['traintype']) && $queryData['traintype'] ) {

            $query->where('traintype', $queryData['traintype']);
        }
        // 班別性質
        if ( isset($queryData['type']) && $queryData['type'] ) {

            $query->where('type', $queryData['type']);
        }
        // 年度
        if ( isset($queryData['yerly']) && $queryData['yerly'] ) {

            $query->where('class', 'like', $queryData['yerly'].'%');
        }
        // 辦班院區
        if ( isset($queryData['branch']) && $queryData['branch'] ) {

            $query->where('branch', $queryData['branch']);
        }
        // 班別類型
        if ( isset($queryData['process']) && $queryData['process'] ) {

            $query->where('process', $queryData['process']);
        }
        // 類別1
        if ( isset($queryData['categoryone']) && $queryData['categoryone'] ) {
            $query->where('categoryone', $queryData['categoryone']);
        }
        // $query->whereRaw('LENGTH(class) = 6');

        // 委訓單位**
        $query4 = T04tb::select('class', 'term', 'client');
        if ( isset($queryData['commission']) && $queryData['commission'] ) {

            $query->where('commission', $queryData['commission']);
        }

        $query->where('type','<>','13');
        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 10);

        return $data;
    }

    public function getClassesdata($year){
        if ($year==NULL) return false;

        $query = T01tb::select('*');
        // 年度
        $results = $query->where('yerly', $year)->get()->toArray();
        return $results;
    }

    /** 取得入口網站代碼 **/
    public function getWebPortal($queryData = []){
        $query = S01tb::select('s01tb.code','s01tb.name','s01tb.category',DB::raw("IFNULL(s03tb.name,'') as categoryname"));
        $query->leftJoin('s03tb', function($join){
            $join->on('s01tb.category', '=', 's03tb.category');
            $join->on('s03tb.category', '<>', DB::raw("''"));
        });
        // $query->where('s03tb.category','<>','');
        $query->where('s01tb.type', 'B');
        // 學院代碼
        if ( isset($queryData['code']) && $queryData['code'] ) {
            
            $query->where('s01tb.code',$queryData['code']);
        }
        // 學院專長
        if ( isset($queryData['name']) && $queryData['name'] ) {
            
            $query->where('s01tb.name', 'like', '%'.$queryData['name'].'%');
        }
        
        // 排序
        if ( isset($queryData['_sort_field']) && $queryData['_sort_field'] ) {

            if (in_array($queryData['_sort_field'], ['class'])) {

                $query->orderBy($queryData['_sort_field'], (isset($queryData['_sort_mode']) && $queryData['_sort_mode'])? 'DESC' : 'ASC');
            }
        } else {
            // 預設排序
            $query->orderBy('code');
        }
        $data = $query->paginate((isset($queryData['_paginate_qty']) && $queryData['_paginate_qty'])? $queryData['_paginate_qty'] : 1);

        return $data;
    }

    /**
     * 取得班期計畫表資料
     *
     * @param $class,$term 關鍵字
     * @return mixed
     */
    public function getClassPlanData($class,$term){
        $query = T04tb::select('t04tb.class', 't04tb.term','t04tb.sdate', 't04tb.edate', 't04tb.sponsor', 't01tb.name', 't01tb.period', 't01tb.kind','t01tb.branch','t01tb.object','t01tb.target','t01tb.content','t01tb.yerly','m09tb.username');
        $query->leftjoin('t01tb','t04tb.class','=','t01tb.class');
        $query->leftjoin('m09tb','m09tb.userid','=','t04tb.sponsor');

        $query->where('t04tb.class',$class)->where('t04tb.term',$term);
        $data = $query->first();

        return $data;
    }

    public function getLecture($class,$term){
        $query = T08tb::select('t08tb.class', 't08tb.term','t08tb.idno','t08tb.cname','t08tb.dept','t08tb.position',DB::raw('IFNULL(t09tb.okrate,"")as okrate ,IFNULL( FORMAT(AVG(t09tb.okrate),2),0) as okrateAll'));
        $query->leftJoin('t09tb',function($join){
            $join->on('t08tb.idno','=','t09tb.idno');
            $join->on('t09tb.okrate','<>',DB::raw('0'));
        });
        $query->where('t08tb.class',$class)->where('t08tb.term',$term);
        $query->groupby('idno');
        $data = $query->get()->toarray();

        return $data;
    }
    //** 取得場地借用行事曆**//
    public function getSiteList($queryData = []){
        
        $query = T97tb::select('t97tb.class', 't97tb.term','t97tb.site','t97tb.date','t97tb.stime','t97tb.etime','t97tb.time','edu_classroom.roomname','t01tb.name'); 
        $query->leftJoin('edu_classroom','edu_classroom.roomno','=','t97tb.site');
        $query->leftJoin('t01tb','t01tb.class','=','t97tb.class');
        if(isset($queryData['yerly']) && isset($queryData['month']) ){
            $basedate = str_pad($queryData['yerly'],3,'0',STR_PAD_LEFT).str_pad($queryData['month'],2,'0',STR_PAD_LEFT);
            $query->WHEREBETWEEN('t97tb.date',array($basedate.'00',$basedate.'32'));
        }elseif(isset($queryData['sdate']) && isset($queryData['edate'])){
            $query->WHEREBETWEEN('t97tb.date',array($queryData['sdate'],$queryData['edate']));
        }else{
            return back()->with('result', 0)->with('message', '匯出失敗，查詢條件錯誤');   
        }
        $query->orderBy('t97tb.date');
        if(isset($queryData['groupby'])){
            $query->groupby('site')->groupby('date');
        }
        $data = $query->get()->toarray();
        return $data;
    }

    //** 取得南投場地數據 **//
    public function getSiteData($queryData = [],$type = NULL){

        if($type =='2'){
            $select = '';
        }elseif(is_null($type) ){
            $select = ' ,edu_loanroom.classroomno,edu_classroom.fullname,edu_loansroom.bedno';
        }
        $query = Edu_loanplacelst::select(
            DB::RAW("distinct edu_loanplacelst.id".$select),
            "edu_loanplacelst.applyno",  "edu_loanplacelst.startdate",      "edu_loanplacelst.enddate",
            "edu_loanplacelst.placenum", "edu_loanplacelst.fee",            "edu_loanplacelst.hday",
            "edu_loanplacelst.nday",     "edu_loanplacelst.nfee",           "edu_loanplacelst.hfee",
            "edu_loanplacelst.timestart","edu_loanplacelst.timeend",        "edu_loanplacelst.ndiscount",
            "edu_loanplacelst.hdiscount","edu_loanplacelst.croomclsno",
            "edu_loanplace.orgname",     "edu_loanplace.title",             "edu_loanplace.applyuser",
            "edu_loanplace.num",         "edu_loanplace.paydate",           "edu_loanplace.reason",
            "edu_loanplace.applydate",
            "edu_classroomcls.croomclsname","edu_classroomcls.classroom"  );

        $query->leftjoin('edu_classroomcls','edu_classroomcls.croomclsno','=','edu_loanplacelst.croomclsno' );

        if(is_null($type) ){ // 詳細清單
            $query->leftJoin('edu_loanroom',function($join){ //教室
                $join->on('edu_loanplacelst.applyno','=','edu_loanroom.applyno')
                ->on('edu_loanplacelst.startdate','=','edu_loanroom.applydate')
                ->on('edu_loanplacelst.croomclsno','=','edu_loanroom.croomclsno');
            });
            $query->leftJoin('edu_classroom','edu_classroom.roomno','=','edu_loanroom.classroomno');
            $query->leftJoin('edu_loansroom',function($join){ //寢室
                $join->on('edu_loanplacelst.applyno','=','edu_loansroom.applyno')
                ->on('edu_loanplacelst.startdate','=','edu_loansroom.startdate')
                ->on('edu_loanplacelst.croomclsno','=','edu_loansroom.croomclsno');
            });

        }
         
        
        $query->leftjoin('edu_loanplace','edu_loanplace.applyno','edu_loanplacelst.applyno');
        if(isset($queryData['sdate']) && isset($queryData['edate']) && $queryData['sdate']!='' && $queryData['edate']!=''){
            $query->WHEREBETWEEN('edu_loanplacelst.startdate',array($queryData['sdate'],$queryData['edate']))->orWHEREBETWEEN('edu_loanplacelst.enddate',array($queryData['sdate'],$queryData['edate']));
        }elseif(isset($queryData['applyno']) && $queryData['applyno']!=''){
            $query->WHERE('edu_loanplacelst.applyno',$queryData['applyno']);
        }elseif(isset($queryData['year']) && $queryData['year']!=''){
            $query->WHERE('edu_loanplacelst.startdate', 'like',$queryData['year'].'%');
            $query->orderBy('edu_loanroom.applydate');
        }else{
            return back()->with('result', 0)->with('message', '匯出失敗，查詢條件錯誤');   
        }

        // $query->WHERE('edu_loanplacelst.fee','>','0');
        $data = $query->get()->toarray();
        return $data;
    }
    

}
