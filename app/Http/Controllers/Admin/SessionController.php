<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\SessionService;
use App\Models\S02tb;
use App\Models\T06tb;
use App\Models\T22tb;
use App\Models\T36tb;
use App\Models\T37tb;
use App\Models\T38tb;
use App\Models\T97tb;
use App\Models\M14tb;
use App\Models\Edu_classroom;

use DB;

class SessionController extends Controller
{
    /**
     * SessionController constructor.
     * @param SessionService $sessionService
     */
    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 年度
        $queryData['yerly'] = is_null($request->get('yerly') )? date('Y')-1911: $request->get('yerly');
        // 會議代號
        $queryData['meet'] = $request->get('meet');
        // 會議名稱
        $queryData['name'] = $request->get('name');
        // 開始時間
        $queryData['sdate'] = $request->get('sdate');
        // 結束時間
        $queryData['edate'] = $request->get('edate');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        if(empty($request->all())) {
            $queryData['choices'] = $this->_get_year_list();
            return view('admin/session/list', compact('queryData'));
        }
        // 取得列表資料
        $data = $this->sessionService->getSessionList($queryData);
        $queryData['choices'] = $this->_get_year_list();
        return view('admin/session/list', compact('data', 'queryData'));
    }

    /**
     * 新增頁
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {   
        $Nantoulist = Edu_classroom::select('roomno','roomname')->get();
        $Taipeilist = M14tb::get();
        return view('admin/session/form', compact('Nantoulist','Taipeilist') );
    }

    /**
     * 新增處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // 取得POST資料
        $data = $request->all();
        // var_dump($data);exit();
        if( $data['meet'] =='' || $data['number'] =='' || $data['serno'] =='' || $data['name'] =='' || $data['sdate'] =='' || $data['edate'] ==''  ) return back()->with('result', '0')->with('message', '錯誤!資料不全');
        // 會議代號
        $data['meet'] = $data['meet'].$data['number'];  //新版本 代號+TWYmmdd
        unset($data['number'],$data['_token']);
        // 取得編號 (已用ajax回傳)
        // $data['serno'] = T38tb::where('meet', $data['meet'])->max('serno') + 1;
        // $data['serno'] = str_pad($data['serno'] ,2,'0',STR_PAD_LEFT);
        // 取得教室
        for ($i=1;$i<4;$i++){
            if($data['branch'.$i]==1){
                $data['site'.$i] = $data['siteT'.$i]==''? '':str_pad($data['siteT'.$i] ,2,'0',STR_PAD_LEFT);
            }elseif($data['branch'.$i]==2){
                $data['site'.$i] = $data['siteN'.$i]==''? '':str_pad($data['siteN'.$i] ,2,'0',STR_PAD_LEFT);
            }else{
                $data['site'.$i] ='';
            }
            unset($data['siteN'.$i],$data['siteT'.$i]);
        }
        if($data['sdate']=='' || $data['edate']==''){ //缺少時間 不預約教室
            unset($data['sdate'],$data['edate'],$data['branch1'],$data['branch2'],$data['branch3'],$data['site1'],$data['site2'],$data['site3']);
            $check = array('result'=>'1','msg'=>'');
            
        }else{  //檢查教室
            $check = $this->checksite($data);
            if($check['result']=='0')  return back()->with('result', '0')->with('message', $check['msg']);
            // 會議日期列表
            $datelist = $this->getdatelist($data['sdate'],$data['edate']);
        }
        $timebase = $this->gettime($data['time']);
        DB::beginTransaction();
        try{
            if($check['result']!=0){
                //寫入教室
                $this->reservation($data,$datelist,$timebase);
                // if($check['result']==0)  return back()->with('result', '0')->with('message', $check['msg']);
            }
            T38tb::create($data);
            DB::commit();
            return redirect('/admin/session/'.$data['meet'].$data['serno'])->with('result', '1')->with('message', '新增成功!');
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message', $check['msg']);
        }
    }
    
    /**
     * 顯示頁
     *
     * @param $meet_serno
     */
    public function show($meet_serno)
    {
        return $this->edit($meet_serno);
    }

    /**
     * 編輯頁
     *
     * @param $meet_serno
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($meet_serno)
    {   
        $serno =  substr($meet_serno, -2); 
        $meet =  substr($meet_serno, 0,-2);
        $data = T38tb::where('meet',$meet)->where('serno',$serno)->first()->toarray();

        if ( ! $data) {

            return view('admin/errors/error');
        }
        $Nantoulist = Edu_classroom::select('roomno','roomname')->get();
        $Taipeilist = M14tb::get();
        return view('admin/session/form', compact('data','Nantoulist','Taipeilist'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $check = array('result'=>'1','msg'=>'');
        // 取得POST資料
        $olddata = $data = $request->all();
        $data['meet'] = $data['number'];
        unset($data['_method'], $data['_token'],$data['number']);
        $Nantoulist = Edu_classroom::select('roomno','roomname')->get();
        $Taipeilist = M14tb::get();
        // 取得教室
        for ($i=1;$i<4;$i++){
            if($data['branch'.$i]==1){
                $data['site'.$i] = $data['siteT'.$i]==''? '':str_pad($data['siteT'.$i] ,2,'0',STR_PAD_LEFT);
            }elseif($data['branch'.$i]==2){
                $data['site'.$i] = $data['siteN'.$i]==''? '':str_pad($data['siteN'.$i] ,2,'0',STR_PAD_LEFT);
            }else{
                $data['site'.$i] ='';
            }
            unset($data['siteN'.$i],$data['siteT'.$i]);
        }
        if($data['sdate']=='' || $data['edate']==''){ //缺少時間 不預約教室
            unset($data['sdate'],$data['edate'],$data['branch1'],$data['branch2'],$data['branch3'],$data['site1'],$data['site2'],$data['site3']);
            $check['result'] = 0;
        }
        
        
        $timebase = $this->gettime($data['time']);
        DB::beginTransaction();
        try{
            if($check['result']!='0')  { 
                // 刪除預約場地
                T36tb::where('class',$data['meet'])->where('term',$data['serno'])->delete();
                T22tb::where('class',$data['meet'])->where('term',$data['serno'])->delete();
                T97tb::where('class',$data['meet'])->where('term',$data['serno'])->delete();
                T37tb::where('class',$data['meet'])->where('term',$data['serno'])->delete();
                //檢查教室
                $check = $this->checksite($data);
                if($check['result']=='0')  { // 錯誤 仍保存輸入資料
                    echo "<script>alert ('". $check['msg']."');</script>";    
                    return view('admin/session/form', compact('data','Nantoulist','Taipeilist'));
                }
                // 會議日期列表
                $datelist = $this->getdatelist($data['sdate'],$data['edate']);       
                //寫入
                $this->reservation($data,$datelist,$timebase);
                // if($check['result']=='0')  return back()->with('result', '0')->with('message', $check['msg']);
                // if($check['result']=='0')  { // 錯誤 仍保存輸入資料
                //     echo "<script>alert ('". $check['msg']."');</script>";    
                //     return view('admin/session/form', compact('data','Nantoulist','Taipeilist'));
                // }
            }
            T38tb::where('meet',$data['meet'])->where('serno',$data['serno'])->update($data);
            DB::commit();
            return redirect('/admin/session/'.$data['meet'].$data['serno'])->with('result', '1')->with('message', '新增成功!');
        }catch ( Exception $e ){
            DB::rollback();
            var_dump($e->getMessage());
            die;
            return view('admin/session/form', compact('data','Nantoulist','Taipeilist'));
        }    
    }

    /**
     * 刪除處理
     *
     * @param $meet_serno
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($meet_serno){   
        $data['serno'] =  substr($meet_serno, -2); 
        $data['meet'] =  substr($meet_serno, 0,-2);
        if ($data['meet']) {

            T38tb::where('meet', $data['meet'])->where('serno',$data['serno'])->delete();
            // 刪除預約場地
            t36tb::where('class',$data['meet'])->where('term',$data['serno'])->delete();
            t22tb::where('class',$data['meet'])->where('term',$data['serno'])->delete();
            t97tb::where('class',$data['meet'])->where('term',$data['serno'])->delete();
            return redirect('/admin/session')->with('result', '1')->with('message', '刪除成功!');
        } else {
            return back()->with('result', '0')->with('message', '刪除失敗!');
        }
    }
    // ajax getserno
    public function getserno(Request $request){
        $data = $request->all();
        $meet = $data['meet'].$data['number'];
        $serno = T38tb::where('meet', $meet)->max('serno');
        if(is_null($serno)){
            $serno = 1;
        }else{
            $serno = $serno +1;
        }
        $serno = str_pad($serno,2,'0',STR_PAD_LEFT);
        echo json_encode(array('status' => '0' , 'msg' => $serno));
        exit;

    }
    // 回傳日期列表return array
    private function getdatelist($sdate,$edate){ 
        $datelist =array();
        $newsdate = (substr($sdate,0,3)+1911).substr($sdate,3);
        $newedate = (substr($edate,0,3)+1911).substr($edate,3);
        $i=1;
        while(true){
            $datelist[] = $sdate;
            if($newsdate==$newedate){
                break;
            }elseif($i==366){
                return 'error';
                break;
            }else{
                $newsdate = date('Ymd',strtotime($newsdate .' +1 Day'));
                $sdate = (date('Y',strtotime($newsdate))-1911).date('md',strtotime($newsdate));
                $i++;
            }
        }
        return $datelist;
    }

    // 場地檢查
    private function checksite($data=array() ){
        $errormsg = '該時段的場地已被預約:';
        for($i=1;$i<4;$i++){
            if($data['site'.$i]=='' || $data['branch'.$i]=='' || !isset($data['site'.$i]) ){
                continue;
            }
            //檢查課程行事曆有無衝突
            $check36 = T36tb::where('class','<>',$data['meet'])->where('site',$data['site'.$i])->where('site_branch',$data['branch'.$i])->whereBetween('date',array($data['sdate'],$data['edate']))->count();
            if($check36 >0) return array('result'=>'0','msg'=>'調整失敗，該時段的場地已被排定!!');
            //檢查實際教室有無衝突
            if($data['branch'.$i]=='2'){ //南投
                if($data['time']=='D'){
                    $check = T97tb::select('date','class','time')->whereBetween('date',array($data['sdate'],$data['edate']))->whereIn('time',array('A','B','D'))->where('site',$data['site'.$i])->get()->toarray();
                }elseif($data['time']=='E'){
                    $check = T97tb::select('date','class','time')->whereBetween('date',array($data['sdate'],$data['edate']))->whereIn('time',array('A','B','C','E'))->where('site',$data['site'.$i])->get()->toarray();
                }else{
                    $check = T97tb::select('date','class','time')->whereBetween('date',array($data['sdate'],$data['edate']))->where('time',$data['time'])->where('site',$data['site'.$i])->get()->toarray();
                }
            }else{  //台北
                if($data['time']=='D'){
                    $check = T22tb::select('date','class','time')->whereBetween('date',array($data['sdate'],$data['edate']))->whereIn('time',array('A','B','D'))->where('site',$data['site'.$i])->get()->toarray();
                }elseif($data['time']=='E'){
                    $check = T22tb::select('date','class','time')->whereBetween('date',array($data['sdate'],$data['edate']))->whereIn('time',array('A','B','C','E'))->where('site',$data['site'.$i])->get()->toarray();
                }else{
                    $check = T22tb::select('date','class','time')->whereBetween('date',array($data['sdate'],$data['edate']))->where('time',$data['time'])->where('site',$data['site'.$i])->get()->toarray();
                }
            }

            if(count($check)>0) {
                foreach ($check as $key => $value) {
                    $errormsg .= $value['date'].'時段'.$value['time'].'，預約班別'.$value['class'].'。';
                }
            }
        }
        if(strlen($errormsg)>32){
            return array('result'=>'0','msg'=>$errormsg);
        }else{
            return array('result'=>'1','msg'=>'');
        }
        
    }

    //場地預約
    private function reservation($data,$datearray,$timebase){
        $sdateVids = (substr($data['sdate'], 0,-4)+1911).substr($data['sdate'], -4);
        $freezeday = s02tb::select('weekly','monthly')->first(); /*凍結日(週、月)(2、05) */
        $affirmVids = date('Ymd',strtotime($sdateVids .' -'.(date('w',strtotime($sdateVids))+8-$freezeday['weekly']).'Day')); //確認凍結日
        $requestVids = date('Ym',strtotime($sdateVids .' -1 month')).$freezeday['monthly'];  //需求凍結日
        
        //寫入
        for($h=1;$h<4;$h++){
            if($data['site'.$h]=='' || $data['branch'.$h]==''){
                continue;
            }
            // '檢查開始日期是否已過確認凍結日 
            // if(strtotime($affirmVids) < strtotime('now') && $data['branch'.$j]=='1') return array('result'=>'0','msg'=>'失敗!開始日期已過確認凍結日期!');
            for($i=0;$i<count($datearray);$i++){
                $indata = array();
                $indata['class'] = $data['meet'];
                $indata['term'] = $data['serno'];
                $indata['site'] = $data['site'.$h];
                $indata['date'] = $datearray[$i];
                $indata['site_branch'] = $data['branch'.$h];
                T36tb::insert($indata);
                unset($indata['site_branch']);

                if($data['branch'.$h]=='1'){
                    $indata['cnt'] = $data['cnt'];
                    // $indata['reserve'] = $user_data->userid; // **預約人userid
                    $indata['reserve'] = $data['sponsor']; // **預約人userid
                    $indata['liaison'] = $data['sponsor'];
                    $indata['usertype'] = '1';  // 預設-學院
                    $indata['seattype'] = 'C';
                    $indata['request'] = (date('Y',strtotime($requestVids))-1911).date('md',strtotime($requestVids)); //需求凍結日
                    $indata['affirm'] = (date('Y',strtotime($affirmVids))-1911).date('md',strtotime($affirmVids)); //確認凍結日
                    if($data['site'.$h]=='404' || $data['site'.$h]=='405'){
                        $indata['status'] = 'Y';
                    }else{
                        $indata['status'] = 'N';
                    }
                    for($j=0;$j<count($timebase);$j++){
                        unset($indata['type']);
                        $indata['stime'] = $timebase[$j]['stime'];
                        $indata['etime'] = $timebase[$j]['etime'];
                        $indata['time'] = $timebase[$j]['time'];
                        T22tb::insert($indata);
                        for($k=1;$k<3;$k++){
                            unset($indata['affirm'],$indata['status'],$indata['usertype']);
                            $indata['type'] = $k;
                            T37tb::insert($indata);
                        }
                    }
                }else{ 
                    for($j=0;$j<count($timebase);$j++){
                        unset($indata['type']);
                        $indata['stime'] = $timebase[$j]['stime'];
                        $indata['etime'] = $timebase[$j]['etime'];
                        $indata['time'] = $timebase[$j]['time'];
                        T97tb::insert($indata);
                        for($k=1;$k<3;$k++){
                            $indata['type'] = $k;
                            T37tb::insert($indata);
                        }
                    }
                }
            }
        }   
    }
    //取得時間
    public function gettime($timetype=NULL){ //A:早上 B:下午 C:晚間 D:白天(A+B) E:全天(A+B+C)
        $data = array();
        if($timetype=='A'){
            $data[0]['time'] = 'A';
            $data[0]['stime'] = '0830';
            $data[0]['etime'] = '1200';
        }elseif($timetype=='B'){
            $data[0]['time'] = 'B';
            $data[0]['stime'] = '1300';
            $data[0]['etime'] = '1630';
        }elseif($timetype=='C'){
            $data[0]['time'] = 'C';
            $data[0]['stime'] = '1800';
            $data[0]['etime'] = '2130';
        }elseif(is_null($timetype) || $timetype=='D'){  //預設
            $data[0]['time'] = 'A';
            $data[0]['stime'] = '0830';
            $data[0]['etime'] = '1200';
            $data[1]['time'] = 'B';
            $data[1]['stime'] = '1300';
            $data[1]['etime'] = '1630';
        }elseif($timetype=='E'){
            $data[0]['time'] = 'A';
            $data[0]['stime'] = '0830';
            $data[0]['etime'] = '1200';
            $data[1]['time'] = 'B';
            $data[1]['stime'] = '1300';
            $data[1]['etime'] = '1630';
            $data[2]['time'] = 'C';
            $data[2]['stime'] = '1800';
            $data[2]['etime'] = '2130';
        }else{
            return FALSE;
        }
        return $data;
    }
    public function _get_year_list()
    {
        $year_list = array();
        $year_now = date('Y');
        $this_yesr = $year_now - 1910;

        for($i=$this_yesr; $i>=90; $i--){
            $year_list[$i] = $i;
        }
        // jd($year_list,1);
        return $year_list;
    }
}
