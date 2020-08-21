<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\RoomSetService;
use App\Services\BedroomDistributionService;
use DB;

class RoomSetController extends Controller
{
    public function __construct(RoomSetService $roomSetService,BedroomDistributionService $bedroomDistributionService)
    {
        $this->roomSetService = $roomSetService;
        $this->bedroomDistributionService = $bedroomDistributionService;
        setProgid('roomset');
    }

    public function index(Request $request)
    {   
        $queryData = $request->only([
            'year',                 // 年度
            'period',               // 期別
            'class',                // 班號
            'classname',            // 班別名稱
            'process',              // 班別類型
            'sdate1',               // 開訓日期範圍(起)
            'sdate2',               // 開訓日期範圍(訖)
            'edate1',               // 結訓日期範圍(起)
            'edate2',               // 結訓日期範圍(訖)
            'courseStartDate',      // 在訓日期範圍(起)
            'courseEndDate',        // 在訓日期範圍(訖)
            '_paginate_qty',        // 分頁資料數量
            'auto',
        ]);

        $auto = $request->only('auto');
        if(intval($auto['auto']) == '1'){   
            $allData = $request->all();
            $courseStartDate = $request->only('courseStartDate');
            $courseEndDate = $request->only('courseEndDate');

            $checkSameWeeks = $this->roomSetService->checkSameWeek($courseStartDate['courseStartDate'],$courseEndDate['courseEndDate']);

            if ($checkSameWeeks){
                $specific = array();
                $other = array();
                foreach ($allData as $key => $value) {
                    if(preg_match("/autoClass/",$key)){
                        if(intval($value) > 0){
                            $tmpCut = explode('_', $key);
                            $tmpArray = array();
                            $tmpArray['class'] = $tmpCut[1];
                            $tmpArray['term'] = $tmpCut[2];
                            $tmpArray['longclass'] = $tmpCut[3];

                            $specific[$value] = $tmpArray;
                            ksort($specific);
                        } else {
                            $tmpCut = explode('_', $key);
                            $tmpArray = array();
                            $tmpArray['class'] = $tmpCut[1];
                            $tmpArray['term'] = $tmpCut[2];
                            $tmpArray['longclass'] = $tmpCut[3];

                            array_push($other, $tmpArray);
                        }
                    }
                }

                $updateStauts = false;
                if(count($specific) > 0){
                    try {
                        DB::beginTransaction();
                        $this->roomSetService->specificProcess($specific,$courseStartDate,$courseEndDate);
                        DB::commit();
                        $updateStauts = true;
                    } catch (\Exception $e) {
                        DB::rollback();
                        var_dump($e->getMessage());
                        die;
                    }   
                    
                }

                if(count($other) > 0){
                    try {
                        DB::beginTransaction();
                        $this->roomSetService->otherProcess($other,$courseStartDate,$courseEndDate);
                        DB::commit();
                        $updateStauts = true;   
                    } catch (\Exception $e) {
                        DB::rollback();
                        var_dump($e->getMessage());
                        die;
                    } 
                }
                
                if($updateStauts){
                    $return_url = '/admin/roomset?courseStartDate='.$courseStartDate['courseStartDate'].'&courseEndDate='.$courseEndDate['courseEndDate'].'&auto=2';
                    return redirect($return_url)->with('result', '1')->with('message', '自動安排成功');
                }

                // echo '<pre>';
                // print_r($request->all());
                // print_r($specific);
                // print_r($other);
                // die();
            } else {
                return redirect('/admin/roomset')->with('result', '2')->with('message', '在訓日期不同週');
            }
        }

        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 10;
        $data = $this->roomSetService->getListForRoomSet($queryData);

        foreach($data as $value){
            $value['totalMaleCount'] = $this->roomSetService->getStudentCount($value->class,$value->term,'M');
            $value['totalFemaleCount'] = $this->roomSetService->getStudentCount($value->class,$value->term,'F');
            $value['dormMaleCount'] = $this->roomSetService->getStudentCount($value->class,$value->term,'M','Y');
            $value['dormFemaleCount'] = $this->roomSetService->getStudentCount($value->class,$value->term,'F','Y');
            $value['hasBedMaleCount'] = $this->roomSetService->getStudentCount($value->class,$value->term,'M','Y','Y');
            $value['hasBedFemaleCount'] = $this->roomSetService->getStudentCount($value->class,$value->term,'F','Y','Y');

            $courseDate = $this->roomSetService->getLongClass($value->class,$value->term)->toArray(); 
            if(count($courseDate) > 1){
                $firstDate = '';
                $lastDate = '';
                $y=intval(substr($courseDate[0]['date'],0,3))+1911;
                $m=intval(substr($courseDate[0]['date'],3,2));
                $d=intval(substr($courseDate[0]['date'],5,2));
                $firstDate = $y.'-'.$m.'-'.$d;
                $firstDate = strtotime($firstDate);

                $y=intval(substr($courseDate[count($courseDate)-1]['date'],0,3))+1911;
                $m=intval(substr($courseDate[count($courseDate)-1]['date'],3,2));
                $d=intval(substr($courseDate[count($courseDate)-1]['date'],5,2));
                $lastDate = $y.'-'.$m.'-'.$d;
                $lastDate = strtotime($lastDate);

                $afweek = date('w',$lastDate);
                $mintime = $lastDate - $afweek * 3600*24;
                $maxtime = $lastDate + (7-$afweek)*3600*24;
                if ( $firstDate >= $mintime && $firstDate <= $maxtime){
                    $value['longclass'] = 'N';
                } else {
                    $value['longclass'] = 'Y';
                }     
            } else {
                $value['longclass'] = 'N';
            }
        }

        return view("/admin/roomset/index",compact('queryData','data'));
    }

    public function export($sdate,$edate)
    {
        $this->bedroomDistributionService->export($sdate,$edate);
        exit;
    }

    public function autoSetAgain($class,$term,$staystartdate,$stayenddate,$longclass,$week)
    {
        $updateStauts = true;
        try {
            DB::beginTransaction();
            $status = $this->roomSetService->autoSetAgain($class,$term,$staystartdate,$stayenddate,$longclass,$week);
            if($status){
                DB::commit();
            } else {
                $updateStauts = false;  
            }
        } catch (\Exception $e) {
            DB::rollback();
            var_dump($e->getMessage());
            die;
        } 

        if($updateStauts){
            $return_url = '/admin/roomset?class='.$class.'&period='.$term;
            return redirect($return_url)->with('result', '1')->with('message', '再次自動安排成功');
        }
    }

    public function editRoomset($class,$term)
    {
        $data = $this->roomSetService->getInfoForEditRoomset($class,$term);

        return view("/admin/roomset/editRoomset",compact('data'));
    }

    public function editLongRoomset($class,$term)
    {
        $data = $this->roomSetService->getInfoForEditRoomset($class,$term);
        $amount['dormMaleCount'] = $this->roomSetService->getStudentCount($class,$term,'M','Y');
        $amount['dormFemaleCount'] = $this->roomSetService->getStudentCount($class,$term,'F','Y');
        $courseDate = $this->roomSetService->getLongClass($class,$term)->toArray();
        $dt = $this->roomSetService->getStayweeks($class,$term)->toArray();

        if(empty($dt) && !empty($courseDate)){
            $this->roomSetService->createWeeks($class,$term,$courseDate);
            $dt = $this->roomSetService->getStayweeks($class,$term)->toArray();
            // $dt=array();
            // $weekstart=0;
            // $i=0;
            // foreach ($courseDate as $key => $value) {
            //     $y=intval(substr($value['date'],0,3))+1911;
            //     $m=intval(substr($value['date'],3,2));
            //     $d=intval(substr($value['date'],5,2));
            //     $toUnixtime=mktime(0,0,0,$m,$d,$y);
            //     $w=date('w',$toUnixtime);

            //     if($w==0){
            //         $time = $toUnixtime + (-6*86400);
            //         $y=date('Y', $time)-1911;
            //         $m=date('m', $time);
            //         $d=date('d', $time);

            //         $weekmonday = str_pad($y, 3,'0',STR_PAD_LEFT).str_pad($m, 2,'0',STR_PAD_LEFT).str_pad($d, 2,'0',STR_PAD_LEFT);
            //     } else {
            //         $j=$w-1;
            //         $time = $toUnixtime + (($j*-1)*86400);
            //         $y=date('Y', $time)-1911;
            //         $m=date('m', $time);
            //         $d=date('d', $time);

            //         $weekmonday = str_pad($y, 3,'0',STR_PAD_LEFT).str_pad($m, 2,'0',STR_PAD_LEFT).str_pad($d, 2,'0',STR_PAD_LEFT);
            //     }

            //     if ($weekstart != $weekmonday){
            //         $i++;
            //         $rec=array();
            //         $rec['week']=$i;
            //         $rec['staystartdate']=$value['date'];
            //         $rec['stayenddate']=$value['date'];
            //         $rec['staystarttime']='1';
            //         $rec['stayendtime']='1';
            //         $rec['washing']='0';
            //         $dt[$i]=$rec;

            //         $weekstart=$weekmonday;
            //     } else {
            //         $dt[$i]['stayenddate'] = $value['date'];
            //     }
            // }
        }

        foreach ($dt as $key => $value) {
            $dt[$key]['hasBedMaleCount'] = $this->roomSetService->getLongStudentCount($class,$term,$value['week'],'M','Y');
            $dt[$key]['hasBedFemaleCount'] = $this->roomSetService->getLongStudentCount($class,$term,$value['week'],'F','Y');
        }

        return view("/admin/roomset/editLongRoomset",compact('data','courseDate','dt','amount'));
    }

    public function updateRoomset(Request $request)
    {
        $rules = array(
            'staystartdate' => 'required',
            'stayenddate' => 'required',
        );

        $messages = array(
            'staystartdate.required' => '請填寫住宿日期起',
            'stayenddate.required' => '請填寫住宿日期迄'
        );

        $this->validate($request, $rules, $messages);

        $updateData = $request->only([
                        'lock',
                        'staystartdate',
                        'stayenddate',
                        'staystarttime',
                        'stayendtime'
                    ]);

        $updateKey = $request->only([
                        'class',
                        'term'
                    ]);

        try {
            $this->roomSetService->updateRoomset($updateData,$updateKey);

            return redirect('/admin/roomset')->with('result', '1')->with('message', '更新成功');
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            die;
        }   
 
    }

    public function updateLongRoomset(Request $request)
    {
        $lock = $request->only(['lock']);
        $updateKey = array();
        $updateKey = $request->only(['class','term']);
       
        if(!empty($lock['lock'])){ 
            try {
                $this->roomSetService->updateRoomset($lock,$updateKey);
            } catch (\Exception $e) {
                var_dump($e->getMessage());
                die;
            }   
        }

        $class = $request->only(['class']);
        $term = $request->only(['term']);
        $weeks = $request->only(['weeks']);
        $sdates = $request->only(['sdates']);
        $stimes = $request->only(['stimes']);
        $edates = $request->only(['edates']);
        $etimes = $request->only(['etimes']);
        $washings = $request->only(['washings']);
        $count = count($weeks['weeks']);
    
        for($i=0;$i<=$count-1;$i++){
            $updateData = array();

            $updateData['class'] = $class['class'];
            $updateData['term'] = $term['term'];
            $updateData['week'] = $weeks['weeks'][$i];
            $updateData['staystartdate'] = $sdates['sdates'][$i];
            $updateData['staystarttime'] = $stimes['stimes'][$i];
            $updateData['stayenddate'] = $edates['edates'][$i];
            $updateData['stayendtime'] = $etimes['etimes'][$i];
            $updateKey['week'] = $weeks['weeks'][$i];
            if(isset($washings['washings'][$i])){
                $updateData['washing'] = 1;
            } else {
                $updateData['washing'] = 0;
            }

            try {
                $this->roomSetService->updateStayweeks($updateData,$updateKey);
            } catch (\Exception $e) {
                var_dump($e->getMessage());
                die;
            }   
        }

        $return_url = '/admin/roomset/editLongRoomset/'.$class['class'].'/'.$term['term'];
        return redirect($return_url)->with('result', '1')->with('message', '更新成功');
    }

    public function cancelRoomset($class,$term,$sex)
    {
        try {
            DB::beginTransaction();
            $this->roomSetService->cancelRoomset($class,$term,$sex);
            DB::commit();

            return redirect('/admin/roomset')->with('result', '1')->with('message', '取消成功');
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            die;
        }  
    }

    public function cancelLongRoomset($class,$term,$week,$sex)
    {
        try {
            DB::beginTransaction();
            $this->roomSetService->resetLongBed($class,$term,$week,$sex);
            DB::commit();

            $return_url = '/admin/roomset/editLongRoomset/'.$class.'/'.$term;
            return redirect($return_url)->with('result', '1')->with('message', '取消成功');
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            die;
        }  
    }

    public function bedSet($class,$term,$sex)
    {
        $data = $this->roomSetService->getInfoForEditRoomset($class,$term);
        $dormStudent = $this->roomSetService->getDormStudent($class,$term,$sex);

        return view("/admin/roomset/bedSet",compact('data','dormStudent','sex'));
    }

    public function longBedSet($class,$term,$week,$sex)
    {
        $data = $this->roomSetService->getInfoFromStayweeks($class,$term,$week);
        $dormStudent = $this->roomSetService->getLongDormStudent($class,$term,$sex,$week);

        return view("/admin/roomset/longBedSet",compact('data','dormStudent','sex'));
    }

    public function updateBedset(Request $request)
    {
        $getData = $request->all();
        $updateKey = array();
        $class = $getData['class'];
        $term = $getData['term'];
        $sex = $getData['sex'];
        $updateKey['class'] = $getData['class'];
        $updateKey['term'] = $getData['term'];
        
        unset($getData['class']);
        unset($getData['term']);
        unset($getData['sex']);

        $updateStatus = false;
        foreach ($getData as $key => $value) {
            if(preg_match("/bedno/",$key)){
                $sAry = explode('_', $key);
                $updateKey['no'] = $sAry[1];

                $updateData = array();

                if(!empty($value)){
                    $updateData['bedno'] = $value;
                } else {
                    $updateData['bedno'] = null;
                }

                try {
                    $this->roomSetService->updateBedset($updateData,$updateKey);
                    $updateStatus = true;
                } catch (\Exception $e) {
                    var_dump($e->getMessage());
                    die;
                }   
            }

            if(preg_match("/floorno/",$key)){
                $sAry = explode('_', $key);
                $updateKey['no'] = $sAry[1];

                $updateData = array();

                if(!empty($value)){
                    $updateData['floorno'] = $value;
                } else {
                    $updateData['floorno'] = null;
                }

                try {
                    $this->roomSetService->updateBedset($updateData,$updateKey);
                    $updateStatus = true;
                } catch (\Exception $e) {
                    var_dump($e->getMessage());
                    die;
                }   
            }
        }

        if($updateStatus){
            $return_url = '/admin/roomset/bedSet/'.$class.'/'.$term.'/'.$sex;
            return redirect($return_url)->with('result', '1')->with('message', '更新成功');
        } 
    }

    public function updateLongBedset(Request $request)
    {
        $getData = $request->all();
        $updateKey = array();
        $class = $getData['class'];
        $term = $getData['term'];
        $sex = $getData['sex'];
        $week = $getData['week'];
        $updateKey['class'] = $getData['class'];
        $updateKey['term'] = $getData['term'];
        $updateKey['week'] = $getData['week'];
        
        unset($getData['class']);
        unset($getData['term']);
        unset($getData['sex']);
        unset($getData['week']);

        $updateStatus = false;
        foreach ($getData as $key => $value) {
            if(preg_match("/bedno/",$key)){
                $sAry = explode('_', $key);
                $updateKey['idno'] = $sAry[1];

                $updateData = array();
                $updateData['idno'] = $sAry[1];
                $updateData['class'] = $updateKey['class'];
                $updateData['term'] = $updateKey['term'] ;
                $updateData['week'] = $updateKey['week'];

                if(!empty($value)){
                    $updateData['bedno'] = $value;
                } else {
                    $updateData['bedno'] = null;
                }

                try {
                    $this->roomSetService->updateLongBedset($updateData,$updateKey);
                    $updateStatus = true;
                } catch (\Exception $e) {
                    var_dump($e->getMessage());
                    die;
                }   
            }

            if(preg_match("/floorno/",$key)){
                $sAry = explode('_', $key);
                $updateKey['idno'] = $sAry[1];

                $updateData = array();
                $updateData['idno'] = $sAry[1];
                $updateData['class'] = $updateKey['class'];
                $updateData['term'] = $updateKey['term'] ;
                $updateData['week'] = $updateKey['week'];

                if(!empty($value)){
                    $updateData['floorno'] = $value;
                } else {
                    $updateData['floorno'] = null;
                }

                try {
                    $this->roomSetService->updateLongBedset($updateData,$updateKey);
                    $updateStatus = true;
                } catch (\Exception $e) {
                    var_dump($e->getMessage());
                    die;
                }   
            }
        }

        if($updateStatus){
            $return_url = '/admin/roomset/longBedSet/'.$class.'/'.$term.'/'.$week.'/'.$sex;
            return redirect($return_url)->with('result', '1')->with('message', '更新成功');
        } 
    }

    public function batchUpdateBedInfo(Request $request)
    {
        $getData = $request->all();
        $class = $getData['class'];
        $term = $getData['term'];
        $sex = $getData['sex'];
        $reset = $getData['resetall'];
        $startdate = $getData['staystartdate'].$getData['staystarttime'];
        $enddate = $getData['stayenddate'].$getData['stayendtime'];
        $buildno = $getData['floorno'];
        $bedno_from = $getData['bedroom1'];
        $bedno_end = $getData['bedroom2'];

        if($reset == 'Y'){
            try {
                $this->roomSetService->resetBed($class,$term,$sex);
            } catch (\Exception $e) {
                var_dump($e->getMessage());
                die;
            }   
        }

        $dormStudent = $this->roomSetService->getDormStudent($class,$term,$sex,$reset)->toArray();
        $beds = $this->roomSetService->get_emptybed($startdate,$enddate,$buildno,$bedno_from,$bedno_end,$sex);
            
        $updateData=array();
        $updateKey=array();
        $updateStatus = false;
        foreach ($beds as $key => $value) {
            if (count($dormStudent)==0) {
                break;
            }

            $signup=array_shift($dormStudent); 
            $updateKey['idno'] = $signup['idno'];
            $updateKey['class'] = $class;
            $updateKey['term'] = $term;
            $updateData['bedno'] = $value->bedno;
            $updateData['floorno'] = $value->floorno;

            try {
                $this->roomSetService->updateBedset($updateData,$updateKey);
                $updateStatus = true;
            } catch (\Exception $e) {
                var_dump($e->getMessage());
                die;
            }   
        }

        if($updateStatus){
            $return_url = '/admin/roomset/bedSet/'.$class.'/'.$term.'/'.$sex;
            return redirect($return_url)->with('result', '1')->with('message', '更新成功');
        } 
    }

    public function batchUpdateLongBedInfo(Request $request)
    {
        $getData = $request->all();
        $class = $getData['class'];
        $term = $getData['term'];
        $sex = $getData['sex'];
        $week = $getData['week'];
        $reset = $getData['resetall'];
        $startdate = $getData['staystartdate'].$getData['staystarttime'];
        $enddate = $getData['stayenddate'].$getData['stayendtime'];
        $buildno = $getData['floorno'];
        $bedno_from = $getData['bedroom1'];
        $bedno_end = $getData['bedroom2'];

        if($reset == 'Y'){
            try {
                $this->roomSetService->resetLongBed($class,$term,$week,$sex);
            } catch (\Exception $e) {
                var_dump($e->getMessage());
                die;
            }   
        }

        $dormStudent = $this->roomSetService->getLongDormStudent($class,$term,$sex,$week,$reset)->toArray();
        $beds = $this->roomSetService->get_emptybed($startdate,$enddate,$buildno,$bedno_from,$bedno_end,$sex);
        
        $updateKey=array();
        $updateStatus = false;
        foreach ($beds as $key => $value) {
            if (count($dormStudent)==0) {
                break;
            }

            $signup=array_shift($dormStudent); 
            $updateKey['idno'] = $signup['idno'];
            $updateKey['class'] = $class;
            $updateKey['term'] = $term;
            $updateKey['week'] = $week;

            $updateData=array();
            $updateData['idno'] = $signup['idno'];
            $updateData['class'] = $updateKey['class'];
            $updateData['term'] = $updateKey['term'] ;
            $updateData['week'] = $updateKey['week'];
            $updateData['bedno'] = $value->bedno;
            $updateData['floorno'] = $value->floorno;

            try {
                $this->roomSetService->updateLongBedset($updateData,$updateKey);
                $updateStatus = true;
            } catch (\Exception $e) {
                var_dump($e->getMessage());
                die;
            }   
        }

        if($updateStatus){
            $return_url = '/admin/roomset/longBedSet/'.$class.'/'.$term.'/'.$week.'/'.$sex;
            return redirect($return_url)->with('result', '1')->with('message', '更新成功');
        } 
    }
}

?>