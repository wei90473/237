<?php
namespace App\Repositories;

use App\Models\Edu_loanplacelst;
use App\Repositories\Repository;
use DB;

class EduloanplacelstRepository extends Repository
{
    public function __construct(Edu_loanplacelst $eduLoanplacelst)
    {
        $this->model = $eduLoanplacelst;
    }  

    public function getForSpaceprocessub($applyno,$queryData){
    	$model = $this->model->selectRaw('edu_loanplacelst.*
                                 ,edu_loanplace.applykind,edu_loanplace.num,edu_loanplace.mstay,edu_loanplace.fstay
                                 ,edu_classroomcls.croomclsname,edu_classroomcls.croomclsfullname,edu_classroomcls.classroom
                                 ,ts.name as timestartname,te.name as timeendname')
							->Join('edu_loanplace', 'edu_loanplacelst.applyno', '=', 'edu_loanplace.applyno')
                            ->leftJoin('edu_classroomcls', 'edu_loanplacelst.croomclsno', '=', 'edu_classroomcls.croomclsno')
							->leftJoin('edu_classcode as ts', function($join){
									$join->on('edu_loanplacelst.timestart', '=', 'ts.code')
										 ->on('ts.class', '=', DB::raw('60'));
							  })
                            ->leftJoin('edu_classcode as te', function($join){
                                    $join->on('edu_loanplacelst.timeend', '=', 'te.code')
                                         ->on('te.class', '=', DB::raw('61'));
                              })
							->orderByRaw('edu_loanplacelst.applyno,edu_loanplacelst.croomclsno,edu_loanplacelst.startdate desc');

    	$model->where('edu_loanplacelst.applyno', '=' , $applyno);
        $paginate_qty = (isset($queryData['_paginate_qty']) && $queryData['_paginate_qty']) ? $queryData['_paginate_qty'] : 100;
        $query = $model->paginate($paginate_qty);
        
        $model = $this->model->selectRaw('edu_loanplacelst.applyno,edu_loanplacelst.croomclsno,edu_loanplacelst.startdate,edu_loanplacelst.timestart,edu_loanplacelst.enddate,count(edu_loanroom.id) AS room')
                            ->Join('edu_loanroom', function($join){
                                    $join->on('edu_loanplacelst.applyno', '=', 'edu_loanroom.applyno')
                                         ->on('edu_loanplacelst.croomclsno', '=', 'edu_loanroom.croomclsno')
                                         ->on('edu_loanplacelst.startdate', '=', 'edu_loanroom.applydate');
                            })
                            ->groupBy('edu_loanplacelst.applyno','edu_loanplacelst.croomclsno','edu_loanplacelst.startdate','edu_loanplacelst.timestart','edu_loanplacelst.enddate')
                            ->orderByRaw('edu_loanplacelst.applyno,edu_loanplacelst.croomclsno,edu_loanplacelst.startdate,edu_loanplacelst.timestart,edu_loanplacelst.startdate');
        $model->where('edu_loanplacelst.applyno', '=' , $applyno);
        $roomquery = $model->get();

        $model = $this->model->selectRaw('edu_loanplacelst.applyno,edu_loanplacelst.croomclsno,edu_loanplacelst.startdate,
                                       edu_loansroom.sex,count(edu_loansroom.id) AS sroom')
                            ->Join('edu_loansroom', function($join){
                                    $join->on('edu_loanplacelst.applyno', '=', 'edu_loansroom.applyno')
                                         ->on('edu_loanplacelst.croomclsno', '=', 'edu_loansroom.croomclsno')
                                         ->on('edu_loanplacelst.startdate', '=', 'edu_loansroom.startdate');
                            })
                            ->groupBy('edu_loanplacelst.applyno','edu_loanplacelst.croomclsno','edu_loanplacelst.startdate','edu_loansroom.sex')
                            ->orderByRaw('edu_loanplacelst.applyno,edu_loanplacelst.croomclsno,edu_loanplacelst.startdate');
        $model->where('edu_loanplacelst.applyno', '=' , $applyno);
        $sroomquery = $model->get();

        foreach($query as $q => $queryValue){
            $queryValue['setstatus']='安排';
            if ($queryValue['classroom']==1){
                foreach($roomquery as $room => $roomqueryValue){
                    if (($roomqueryValue['applyno']==$queryValue['applyno']) AND ($roomqueryValue['croomclsno']==$queryValue['croomclsno']) and ($roomqueryValue['startdate']==$queryValue['startdate']) 
                        and ($roomqueryValue['timestart']==$queryValue['timestart']) and ($roomqueryValue['enddate']==$queryValue['enddate'])){
                        $queryValue['setstatus']=$roomqueryValue['room'].'間';
                        break;
                    }
                }               
            }else{
                $mcount=0;
                $fcount=0;
                $s='';
                foreach($sroomquery as $sroom => $sroomqueryValue){
                    if (($sroomqueryValue['applyno']==$queryValue['applyno']) AND ($sroomqueryValue['croomclsno']==$queryValue['croomclsno']) AND ($sroomqueryValue['startdate']==$queryValue['startdate'])){
                        if ($sroomqueryValue['sex']==1) $mcount=$mcount+$sroomqueryValue['sroom'];
                        else $fcount=$fcount+$sroomqueryValue['sroom'];
                    }
                }               
                if ($mcount>0) $s='男:'.$mcount.'床';
                if (!empty($s)) $s=$s.'<br/>';
                if ($fcount>0) $s=$s.'女:'.$fcount.'床';
                if (!empty($s)) $queryValue['setstatus']=$s;
            }
        }

        return $query;
    }
}