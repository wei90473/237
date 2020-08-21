<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ClassesRequirementsService;
use App\Services\Term_processService;
use App\Services\User_groupService;
use App\Models\T01tb;
use App\Models\T04tb;
use App\Models\T06tb;
use App\Models\T22tb;
use App\Models\T23tb; //辦班需求確認檔_台北
use App\Models\T35tb;
use App\Models\T36tb;
use App\Models\T38tb;
use App\Models\S02tb;
use App\Models\S06tb;
use App\Models\Edu_classdemand; // 辦班需求確認檔_南投
use App\Models\Edu_classdemand_stopcook; //止伙明細
use App\Helpers\ModifyLog;
use DB ;
use Auth;

class ClassesRequirementsController extends Controller
{
    public $authority = 0; //修改權限
    public $strDeadLineTime1 = '0940';
    public $strDeadLineTime2 = '1400';
    /**
     * WaitingController constructor.
     * @param ClassesRequirementsService $classesrequirementsService
     */
    public function __construct(ClassesRequirementsService $classesrequirementsService, User_groupService $user_groupService, Term_processService $term_processService)
    {
        $this->classesrequirementsService = $classesrequirementsService;
        $this->term_processService = $term_processService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            $authority_group = array(3,6);
            $user_group_id = explode(',',$user_data->user_group_id);
            foreach ($user_group_id as $value) {
                if(in_array($value, $authority_group)){
                    $this->authority = 1;
                    break;
                }
            }
            if(in_array('classes_requirements', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
        setProgid('classes_requirements');
    }

    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)    {
        $authority = $this->authority;
        // 年度
        $queryData['yerly'] = is_null($request->get('yerly') )? date('Y')-1911: $request->get('yerly');
        // 班號
        $queryData['class'] = $request->get('class');
        // 班別名稱
        $queryData['name'] = $request->get('name');
        // 分班名稱
        $queryData['branchname'] = $request->get('branchname');
        // 取得期別
        $queryData['term'] = $request->get('term');
        // 辦班院區
        $queryData['branch'] = $request->get('branch');
        // 班別類型
        $queryData['process'] = $request->get('process');
        // 班務人員
        $queryData['sponsor'] = $request->get('sponsor');
        // 訓練性質
        $queryData['traintype'] = $request->get('traintype');
        // 班別性質
        $queryData['type'] = $request->get('type');
        // 類別1
        $queryData['categoryone'] = $request->get('categoryone');
        //開訓日期
        $queryData['sdate'] = $request->get('sdate');
        $queryData['edate'] = $request->get('edate');
        $queryData['sdate2'] = $request->get('sdate2');
        $queryData['edate2'] = $request->get('edate2');
        $queryData['sdate3'] = $request->get('sdate3');
        $queryData['edate3'] = $request->get('edate3');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        if(empty($request->all())) {
            $queryData['choices'] = $this->_get_year_list();
            $sess = $request->session()->get('lock_class');
            if($sess){
              $queryData2['class'] = $sess['class'];
              $queryData2['term'] = $sess['term'];
              $queryData2['yerly'] = substr($sess['class'], 0, 3);
              $data = $this->classesrequirementsService->getClassesRequirementsList($queryData2);
              return view('admin/classes_requirements/list', compact('data', 'queryData'));
            }
            return view('admin/classes_requirements/list', compact('queryData'));
        }
        // 取得列表資料
        $data = $this->classesrequirementsService->getClassesRequirementsList($queryData);
        $queryData['choices'] = $this->_get_year_list();
        return view('admin/classes_requirements/list', compact('data', 'queryData'));
    }

    /*
     * 更新單價
     */
    public function unitprice(Request $request){
        $queryData['sdate'] = $request->get('unitprice_begin');
        $queryData['edate'] = $request->get('unitprice_end');
        DB::beginTransaction();
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
        try{
            $update = $this->classesrequirementsService->updataunitprice($queryData);
            DB::commit();
            return back()->with('result', '1')->with('message', '更新完畢');
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message', '更新失敗');
        }

    }
    //辦班需求(確認)編輯頁面
    public function edit($class_term)    {
        $term = $queryData['term'] = substr($class_term, -2);
        $class = $queryData['class'] = substr($class_term, 0,-2);
        $authority = $this->authority;
        $yerly = substr($class_term, 0,3);
        $queryData = $this->classesrequirementsService->getEditList($queryData);
        if(is_null($queryData)) return redirect('/admin/classes_requirements')->with('result', '0')->with('message', '查無此資料！');
        // 開支科目
        $Expenditure = S06tb::select('acccode','accname')->where('yerly',$yerly)->get()->toarray();
        if($queryData['branch']=='1') { //台北
            $data = T23tb::where('class',$class)->where('term',$term)->where('type','5')->orderby('date')->get();
            //批次增刪班別清單
            $list = T01tb::select('class','branch','name')->where('class','like', $yerly.'%')->orderby('class')->get();
            return view('admin/classes_requirements/form', compact('queryData','data','list','Expenditure','authority'));
        }else{ //南投
            $data = Edu_classdemand::where('class',$class)->where('term',$term)->where('type','5')->orderby('date')->get();
            $stopcooklist = Edu_classdemand_stopcook::where('class',$class)->where('term',$term)->orderby('stopdate')->get();
            return view('admin/classes_requirements/form', compact('queryData','data','Expenditure','stopcooklist','authority'));
        }
    }

    // 新增
    public function store(Request $request){

        $data = $request->all();
        if($data['date']=='') return back()->with('result', 0)->with('message', '日期不可為空白');
        // dd($data);
        $deadline = 4;
        $branch = $data['branch'];
        unset($data['_token'],$data['branch']);
        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('classes_requirements', $data['class'], $data['term']);
        if($this->authority==1){
        
        }elseif($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法新增');
        }

        if($branch =='1'){
            $newdate = (substr($data['date'], 0,-4)+1911).substr($data['date'], -4);
            $check = T23tb::select('class','date')->where('class',$data['class'])->where('term',$data['term'])->where('date',$data['date'])->get()->toarray();
            if(count($check)>0){
                return back()->with('result', '0')->with('message', '已有此天的辦班資料，無法新增');
            }
            $data['upddate'] = date('Y-m-d H:i:s');
            $newdate = (substr($data['date'], 0,-4)+1911).substr($data['date'], -4); // 日期轉換西元
            $s02data = S02tb::select('*')->first(); /*凍結日(週、月)(2、05) */
            // 檢查權限
            if(date('Ymd',strtotime($newdate)) > date('Ymd',strtotime('now')) || $this->authority==1){
                $deadline = 0;
            }elseif( $this->authority!=1){ 
                if(strtotime( $newdate.$this->strDeadLineTime2)  < strtotime('now') ){// 超過當日死線2 
                    return back()->with('result', '0')->with('message', '日期:'.$data['date'].'已過日異動時間，無法新增');
                }elseif(strtotime( $newdate.$this->strDeadLineTime1) < strtotime('now') ) {// 當日死線2內可修改晚餐+住宿
                    $deadline = 2;
                }else{ // 當日死線1內可修改午晚餐+住宿
                    $deadline = 1;
                }
            }
            $affirmVids = date('Ymd',strtotime($newdate .' -'.(date('w',strtotime($newdate))+8-$s02data['weekly']).'Day')); //確認凍結日
            $requestVids = date('Ym',strtotime($newdate .' -1 month')).$s02data['monthly'];  //需求凍結日
    
            $data['teaunit']  =             $s02data['teaunit']  ;
            $data['meaunit']  =             $s02data['meaunit']  ;
            $data['lununit']  =             $s02data['lununit']  ;
            $data['dinunit']  =             $s02data['dinunit']  ;
            $data['sinunit']  =             $s02data['sinunit']  ;
            $data['doneunit'] =             $s02data['doneunit'] ;
            $data['dtwounit'] =             $s02data['dtwounit'] ;
            $data['meaunit']  =             $s02data['meaunit']  ;
            if($deadline < 1){
                $data['meacnt']   =  ($data['meacnt']!=null) ?      (int)$data['meacnt']:0;
                $data['meavegan'] =  ($data['meavegan']!=null) ?    (int)$data['meavegan']:0;
                $tabtype          =  $data['tabtype'] == '1'?'午餐':($data['tabtype'] == '2'?'晚餐':'');
                $buftype          =  $data['buftype'] == '1'?'午餐':($data['buftype'] == '2'?'晚餐':'');
                $data['teacnt']   =  ($data['teacnt']!=null) ?      (int)$data['teacnt']:0;         
                $data['teaunit']  =  ($data['teaunit']!=null) ?     (int)$data['teaunit']:0;   
                $data['teatime']  =  isset($data['teatime'])?       (int)$data['teatime']:''; 
                $data['otheramt'] =  ($data['otheramt']!=null) ?    (int)$data['otheramt']:0;   
                $data['bufunit']  =  isset($data['bufunit'])?       (int)$data['bufunit']:0;
                $data['upddate']  =  date('Y-m-d H:i:s');
                $data['request']  =  (date('Y',strtotime($requestVids))-1911).date('md',strtotime($requestVids)); //需求凍結日
                $data['affirm']   =  (date('Y',strtotime($affirmVids))-1911).date('md',strtotime($affirmVids)); //確認凍結日
                $data['siteamt']  =  isset($data['siteamt'])?       (int)$data['siteamt']:0;
            }
            if($deadline < 2 ){
                $data['luncnt']   =  ($data['luncnt']!=null) ?      (int)$data['luncnt']:0;
                $data['lunvegan'] =  ($data['lunvegan']!=null) ?    (int)$data['lunvegan']:0; 
                $data['tabcnt']   =  ($data['tabcnt']!=null) ?      (int)$data['tabcnt']:0;
                $data['tabvegan'] =  ($data['tabvegan']!=null) ?    (int)$data['tabvegan']:0;
                $data['tabunit']  =  ($data['tabunit']!=null) ?     (int)$data['tabunit']:0;
                $data['bufcnt']   =  ($data['bufcnt']!=null) ?      (int)$data['bufcnt']:0;
                $data['bufvegan'] =  ($data['bufvegan']!=null) ?    (int)$data['bufvegan']:0;
            }
            if($deadline < 3){
                $data['sincnt']   =  ($data['sincnt']!=null) ?      (int)$data['sincnt']:0;
                $data['donecnt']  =  ($data['donecnt']!=null) ?     (int)$data['donecnt']:0;
                $data['dtwocnt']  =  ($data['dtwocnt']!=null) ?     (int)$data['dtwocnt']:0;
                $data['lovecnt']  =  ($data['lovecnt']!=null) ?     (int)$data['lovecnt']:0;
                $data['dincnt']   =  ($data['dincnt']!=null) ?      (int)$data['dincnt']:0;
                $data['dinvegan'] =  ($data['dinvegan']!=null) ?    (int)$data['dinvegan']:0;
            }
            
            $content = '班別/會議代號：'.$data['class'].'
            期別/編號：'.$data['term'].'
            日期：'.$data['date'];
            if($deadline < 3){
                $content .= '單人房：'.$data['sincnt'].'
                雙人單床房:'.$data['donecnt'].'
                雙人雙床房:'.$data['dtwocnt'].'
                愛心房:'.$data['lovecnt'];
            }
            $content .= '早餐:'.$data['meacnt'].'早素:'.$data['meavegan'];
            if($deadline < 2 ){
                $content .= '午餐:'.$data['luncnt'].'午素:'.$data['lunvegan'];
            }
            if($deadline < 3){
                $content .= '晚餐:'.$data['dincnt'].'
                晚素:'.$data['dinvegan'];
            }
            if($deadline < 2 ){
                $content .= '訂席桌餐餐種： '.$tabtype.'
                訂席桌餐人數：'.$data['tabcnt'].'
                訂席桌餐素食：'.$data['tabvegan'].'
                訂席桌餐單價：'.$data['tabunit'].'
                自助餐餐種：'.$buftype.'
                自助餐人數：'.$data['bufcnt'].'
                自助餐素食：'.$data['bufvegan'];
            }
            if($deadline < 1){
                $content .= '
                茶點人數：'.$data['teacnt'].'
                茶點單價：'.$data['teaunit'].'
                茶點時間：'.$data['teatime'].'
                其他餐點：'.$data['otheramt'].'
                場租：'.$data['siteamt'].'
                需求凍結日：'.$data['request'].'
                確認凍結日：'.$data['affirm'];
            }
            $logarray = $this->getlog('I',$content);
        }else{  //南投
            $check = Edu_classdemand::where('class',$data['class'])->where('term',$data['term'])->where('date',$data['date'])->count();
            if($check > 0){
                return back()->with('result', '0')->with('message', '已有此天的辦班資料，無法新增!');
            }
            $content = '';
            $rule = array('progid'=>'new','logtable'=>'Edu_classdemand');
            $logarray = $this->getlog('I',$content,$rule);
        }

        // var_dump($data);exit();
        DB::beginTransaction();
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
        try{
            T35tb::create($logarray);
            if($branch =='1'){
                //1:需求 2:確認 3:異動 4:需求確認 5:核銷
                //新增的時候同時作業兩筆  3 / 5
                $data['type'] = 5; 
                T23tb::create($data);
                $sql = DB::getQueryLog();
                $nowdata = T23tb::where('class',$data['class'])->where('term',$data['term'])->where('date',$data['date'])->where('type',$data['type'])->get()->toarray();
                createModifyLog('I','t23tb','',$nowdata,end($sql));
                $data['type'] = 3; 
                T23tb::create($data);
                $sql = DB::getQueryLog();
                $nowdata = T23tb::where('class',$data['class'])->where('term',$data['term'])->where('date',$data['date'])->where('type',$data['type'])->get()->toarray();
                createModifyLog('I','t23tb','',$nowdata,end($sql));
            }else{
                $data['type'] = 3; 
                Edu_classdemand::create($data);
                $sql = DB::getQueryLog();
                $nowdata = Edu_classdemand::where('class',$data['class'])->where('term',$data['term'])->where('date',$data['date'])->where('type',$data['type'])->get()->toarray();
                createModifyLog('I','edu_classdemand','',$nowdata,end($sql));
                $data['type'] = 5; 
                Edu_classdemand::create($data);
                $sql = DB::getQueryLog();
                $nowdata = Edu_classdemand::where('class',$data['class'])->where('term',$data['term'])->where('date',$data['date'])->where('type',$data['type'])->get()->toarray();
                createModifyLog('I','edu_classdemand','',$nowdata,end($sql));
            }
            DB::commit();
            return back()->with('result', '1')->with('message', '新增成功!!');
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message', '新增失敗!!');
        }

    }
    // 編輯
    public function update (Request $request,$date){
        $data = $request->all();
        $deadline = 4;
        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('classes_requirements', $data['class'], $data['term']);
        if($this->authority==1){
        
        }elseif($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法修改');
        }
        $now = (date('Y',strtotime('now'))-1911).date('md',strtotime('now'));
        if($this->authority==1){
        
        }elseif($data['date'] < $now) {
            return back()->with('result', '0')->with('message', '無法修改歷史資料');
        }
        $branch = $data['branch'];
        unset($data['_token'],$data['branch'],$data['_method']);

        if($branch =='1'){
            $T23data = T23tb::where('class',$data['class'])->where('term',$data['term'])->where('date',$data['date'])->where('type','5')->get()->toarray();
            $T23data = $T23data[0]; 
            // dd($T23data);
            if(empty($T23data)){
                return back()->with('result', '0')->with('message', '查無資料');
            }
            $T23data['upddate'] = date('Y-m-d H:i:s');

            // '檢查開課日期是否已過確認凍結日
            $newdate = (substr($data['date'], 0,-4)+1911).substr($data['date'], -4);
            $s02data = S02tb::select('*')->first(); /*凍結日(週、月)(2、05) */
            // 檢查權限
            if(date('Ymd',strtotime($newdate)) > date('Ymd',strtotime('now')) || $this->authority==1){
                $deadline = 0;
            }elseif( $this->authority!=1){ 
                if(strtotime( $newdate.$this->strDeadLineTime2)  < strtotime('now') ){// 超過當日死線2 
                    return back()->with('result', '0')->with('message', '日期:'.$data['date'].'已過日異動時間，無法修改儲存');
                }elseif(strtotime( $newdate.$this->strDeadLineTime1) < strtotime('now') ) {// 當日死線2內可修改晚餐+住宿
                    $deadline = 2;
                    if( $T23data['luncnt']      != $data['luncnt']   || $T23data['lunvegan']    != $data['lunvegan'] ||
                        $T23data['tabcnt']      != $data['tabcnt']   || $T23data['tabvegan']    != $data['tabvegan'] ||
                        $T23data['tabunit']     != $data['tabunit']  || $T23data['bufcnt']      != $data['bufcnt']   ||
                        $T23data['bufvegan']    != $data['bufvegan'] ) {
                        return back()->with('result', '0')->with('message', '不可調改午餐');
                    }
                }else{ // 當日死線1內可修改午晚餐+住宿
                    $deadline = 1;
                }
            }

            if($deadline < 3){
                $T23data['dincnt']      = $data['dincnt'];
                $T23data['dinvegan']    = $data['dinvegan'];
                $T23data['sincnt']      = $data['sincnt'];
                $T23data['donecnt']     = $data['donecnt'];
                $T23data['dtwocnt']     = $data['dtwocnt'];
                $T23data['lovecnt']     = $data['lovecnt'];
            }
            if($deadline < 2){
                $T23data['luncnt']      = $data['luncnt'];
                $T23data['lunvegan']    = $data['lunvegan'];
                $T23data['tabcnt']      = $data['tabcnt'];
                $T23data['tabvegan']    = $data['tabvegan'];
                $T23data['tabunit']     = $data['tabunit'];
                $T23data['bufcnt']      = $data['bufcnt'];
                $T23data['bufvegan']    = $data['bufvegan'];
            }
            if($deadline < 1){
                $data['request'] = $T23data['request'];
                $data['affirm'] = $T23data['affirm'];
                $T23data = $data;
            }
            $tabtype =  $T23data['tabtype'] == '1'?'午餐':($T23data['tabtype'] == '2'?'晚餐':'');
            $buftype =  $T23data['buftype'] == '1'?'午餐':($T23data['buftype'] == '2'?'晚餐':'');
            $content = '班別/會議代號：'.$T23data['class'].'
            期別/編號：'.$T23data['term'].'
            日期：'.$T23data['date'].'
            單人房：'.$T23data['sincnt'].'
            雙人單床房:'.$T23data['donecnt'].'
            雙人雙床房:'.$T23data['dtwocnt'].'
            愛心房:'.$T23data['lovecnt'].'
            早餐:'.$T23data['meacnt'].'
            早素:'.$T23data['meavegan'].'
            午餐:'.$T23data['luncnt'].'
            午素:'.$T23data['lunvegan'].'
            晚餐:'.$T23data['dincnt'].'
            晚素:'.$T23data['dinvegan'].'
            訂席桌餐餐種： '.$tabtype.'
            訂席桌餐人數：'.$T23data['tabcnt'].'
            訂席桌餐素食：'.$T23data['tabvegan'].'
            訂席桌餐單價：'.$T23data['tabunit'].'
            自助餐餐種：'.$buftype.'
            自助餐人數：'.$T23data['bufcnt'].'
            自助餐素食：'.$T23data['bufvegan'].'
            茶點人數：'.$T23data['teacnt'].'
            茶點單價：'.$T23data['teaunit'].'
            茶點時間：'.$T23data['teatime'].'
            其他餐點：'.$T23data['otheramt'].'
            場租：'.$T23data['siteamt'].'
            需求凍結日：'.$T23data['request'].'
            確認凍結日：'.$T23data['affirm'];
            $logarray = $this->getlog('U',$content);
        }else{ //南投
            $check = Edu_classdemand::where('class',$data['class'])->where('term',$data['term'])->where('date',$data['date'])->count();
            if($check==0){
                return back()->with('result', '0')->with('message', '查無資料!');
            }
            $content = '';
            $rule = array('progid'=>'new','logtable'=>'Edu_classdemand');
            $logarray = $this->getlog('U',$content,$rule);
        }
        DB::beginTransaction();
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
        try{
            T35tb::create($logarray);
            $sql = DB::getQueryLog();
            $nowdata = T35tb::where('class',$data['class'])->where('term',$data['term'])->where('date',$data['date'])->where('type',$data['type'])->get()->toarray();
            createModifyLog('I','t35tb','',$nowdata,end($sql));
            if($branch =='1'){  
                $olddata = T23tb::where('class',$data['class'])->where('term',$data['term'])->where('date',$data['date'])->get()->toarray();    
                T23tb::where('class',$data['class'])->where('term',$data['term'])->where('date',$data['date'])->update($T23data);
                $sql = DB::getQueryLog();
                $nowdata = T23tb::where('class',$data['class'])->where('term',$data['term'])->where('date',$data['date'])->get()->toarray();
                createModifyLog('U','t23tb',$olddata,$nowdata,end($sql));
            }else{
                $olddata = Edu_classdemand::where('class',$data['class'])->where('term',$data['term'])->where('date',$data['date'])->get()->toarray();
                Edu_classdemand::where('class',$data['class'])->where('term',$data['term'])->where('date',$data['date'])->update($data);
                $sql = DB::getQueryLog();
                $nowdata = Edu_classdemand::where('class',$data['class'])->where('term',$data['term'])->where('date',$data['date'])->get()->toarray();
                createModifyLog('U','edu_classdemand',$olddata,$nowdata,end($sql));
            }
            DB::commit();
            return back()->with('result', '1')->with('message', '編輯成功!!');
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message', '編輯失敗!!');
        }

    }
    // 新增止伙(南投)
    public function stopcook(Request $request){

        $data = $request->all();

        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('classes_requirements', $data['class'], $data['term']);
        if($this->authority==1){
        
        }elseif($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法新增');
        }

        $check = Edu_classdemand_stopcook::where('class',$data['class'])->where('term',$data['term'])->where('stopdate',$data['stopdate'])->where('cooktype',$data['cooktype'])->count();
        if($check>0){
            return back()->with('result', '0')->with('message', '錯誤，資料重複');
        }else{
            unset($data['_token']);
            Edu_classdemand_stopcook::create($data);
            return back()->with('result', '1')->with('message', '新增完畢');
        }

    }
    //更新開支科目
    public function acccode(Request $request,$class_term){

        $data = $request->all();
        $term = $queryData['term'] = substr($class_term, -2);
        $class = $queryData['class'] = substr($class_term, 0,-2);

        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('classes_requirements', $class, $term);
        if($this->authority==1){
        
        }elseif($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法修改');
        }

        $check = T04tb::where('class',$class)->where('term',$term)->first();
        if(empty($check)) {
            return back()->with('result', '0')->with('message', '開支科目更新失敗!!');
        } 

        $today = (date('Y')-1911).date('md');
        if($check['site_branch']=='1'){
            $request = T23tb::where('class',$queryData['class'])->where('term',$queryData['term'])->max('request');
            // if($today > $request && isset($request))   return back()->with('result', '0')->with('message', '開支科目更新失敗!已超過凍結日期!');

        }
        $update = T04tb::where('class',$class)->where('term',$term)->update(array('kind'=>$data['acccode']));
        return back()->with('result', 1)->with('message', '開支科目更新成功');
    }
    // 刪除
    public function destroy($id){

        $queryData['term'] = substr($id,-9,2);
        $queryData['class'] = substr($id,0,-9);
        $queryData['date'] = substr($id,-7);

        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('classes_requirements', $queryData['class'], $queryData['term']);
        if($this->authority==1){
        
        }elseif($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法刪除');
        }

        if(strlen($id) < 14) return back()->with('result', '1')->with('message', '錯誤!請選擇正確日期');

        $now = (date('Y',strtotime('now'))-1911).date('md',strtotime('now'));
        $t04data = T04tb::select('site_branch')->where('class',$queryData['class'])->where('term',$queryData['term'])->first();
        if($t04data['site_branch']=='1'){
            $T23data = T23tb::where('class',$queryData['class'])->where('term',$queryData['term'])->where('date',$queryData['date'])->where('type','5')->first();
            if($now > $T23data['affirm'] && $this->authority==0) return back()->with('result', '0')->with('message', '上課日期已過確認凍結日，無法刪除');

            $tabtype = $T23data['tabtype'] == '1'?'午餐':($T23data['tabtype'] == '2'?'晚餐':'');
            $buftype = $T23data['buftype'] == '1'?'午餐':($T23data['buftype'] == '2'?'晚餐':'');
            $content = '班別：'.$T23data['class'].'
            期別：'.$T23data['term'].'
            日期：'.$T23data['date'].'
            單人房:'.$T23data['sincnt'].'
            雙人單床房:'.$T23data['donecnt'].'
            雙人雙床房:'.$T23data['dtwocnt'].'
            愛心房:'.$T23data['lovecnt'].'
            早餐:'.$T23data['meacnt'].'
            早素:'.$T23data['meavegan'].'
            午餐:'.$T23data['luncnt'].'
            午素:'.$T23data['lunvegan'].'
            晚餐:'.$T23data['dincnt'].'
            晚素:'.$T23data['dinvegan'].'
            訂席桌餐餐種:'.$tabtype.'
            訂席桌餐人數:'.$T23data['tabcnt'].'
            訂席桌餐素食:'.$T23data['tabvegan'].'
            訂席桌餐單價:'.$T23data['tabunit'].'
            自助餐餐種:'.$buftype.'
            自助餐人數:'.$T23data['bufcnt'].'
            自助餐素食:'.$T23data['bufvegan'].'
            自助餐單價:'.$T23data['bufunit'].'
            茶點人數:'.$T23data['teacnt'].'
            茶點單價:'.$T23data['teaunit'].'
            茶點時間:'.$T23data['teatime'].'
            其他餐點:'.$T23data['otheramt'].'
            場租:'.$T23data['siteamt'].'
            需求凍結日:'.$T23data['request'].'
            確認凍結日:'.$T23data['affirm'];
        }else{
            $classdemandData = Edu_classdemand::where('class',$queryData['class'])->where('term',$queryData['term'])->where('date',$queryData['date'])->first();
            $content = '班別：'.$classdemandData['class'].'
            期別：'.$classdemandData['term'].'
            日期：'.$classdemandData['date'];  //南投暫無log
        }

        $logarray = $this->getlog('D',$content);
        DB::beginTransaction();
        try{
            T35tb::create($logarray);
            if($t04data['site_branch']=='1'){
                T23tb::where('class',$queryData['class'])->where('term',$queryData['term'])->where('date',$queryData['date'])->delete();
            }else{
                Edu_classdemand::where('class',$queryData['class'])->where('term',$queryData['term'])->where('date',$queryData['date'])->delete();
            }
            DB::commit();
            return back()->with('result', '1')->with('message', '刪除成功!');
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message', '刪除失敗!!');
        }

    }
    //週確認新增 ->批次新增
    public function groupstore(Request $request)    {

    
        $data = $request->all();
        if($data['groupterm']=='' || $data['groupclass']=='') return back()->with('result', '0')->with('message', '新增失敗!請重新整理!');

        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('classes_requirements', $data['groupclass'], $data['groupterm']);
        if($this->authority==1){
        
        }elseif($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法新增');
        }


        $classtype = preg_match("/^[A-z]/",$data['groupclass'] );  // 判斷是否為會議 0:班別 1:會議
        // 判斷有無預約場地
        $daylist = T22tb::select('date')->where('class',$data['groupclass'])->where('term',$data['groupterm'])->groupby('date')->get()->toarray();
        if($classtype=='1' && empty($daylist)){
            $t38day = T38tb::select('sdate','edate')->where('meet',$class)->where('serno',$term)->groupby('date')->first();
            $sdate = (substr($t38day['sdate'], 0,-4)+1911).substr($t38day['sdate'], -4);
            $edate = (substr($t38day['edate'], 0,-4)+1911).substr($t38day['edate'], -4);
            $daylist = array();
            while (true) {
                $daylist[] = array('date'=>(substr($sdate, 0,-4)-1911).substr($sdate, -4));
                if($sdate == $edate){
                    break;
                }
                $sdate = date('Ymd',strtotime($sdate .' +1 Day'));
                if($i>365){
                    return back()->with('result', '0')->with('message', 'error!');
                    break;
                }
                $i++;
            }
        }elseif($classtype=='0' && empty($daylist)){
            $daylist = T36tb::select('date')->where('class',$data['groupclass'])->where('term',$data['groupterm'])->groupby('date')->get()->toarray();
        }
        if(empty($daylist))  return back()->with('result', '0')->with('message', '找不到班級或會議的資料，無法批次新增!');

        // $count = count($daylist)+1;


        $live = $this->classesrequirementsService->getLiveList($data);
        $day = T04tb::select('sdate','edate')->where('class',$data['groupclass'])->where('term',$data['groupterm'])->first();
        $t23data = T23tb::select('date')->where('class',$data['groupclass'])->where('term',$data['groupterm'])->groupby('date')->get()->toarray();

        $olddaylist = array();
        foreach ($t23data as $key => $value) {
            $olddaylist[] = $value['date'];
        }

         // $gettimes = T22tb::select('request','affirm')->where('class',$data['groupclass'])->where('term',$data['groupterm'])->first();
        $s02data = S02tb::select('*')->first(); /*凍結日(週、月)(2、05) */
        $newdate = (substr($day['sdate'], 0,-4)+1911).substr($day['sdate'], -4);
        $advance = (date('Y',strtotime($newdate .' -1 Day'))).date('md',strtotime($newdate .' -1 Day'));
        $affirmVids = date('Ymd',strtotime($advance .' -'.(date('w',strtotime($advance))+8-$s02data['weekly']).'Day')); //確認凍結日
        $requestVids = date('Ym',strtotime($advance .' -1 month')).$s02data['monthly'];  //需求凍結日
        $live['request'] = (date('Y',strtotime($requestVids))-1911).date('md',strtotime($requestVids)); //需求凍結日
        $live['affirm'] = (date('Y',strtotime($affirmVids))-1911).date('md',strtotime($affirmVids)); //確認凍結日
        $live['date'] = (date('Y',strtotime($advance))-1911).date('md',strtotime($advance));
    
        $data['upddate']  =              date('Y-m-d H:i:s');
        $data['teaunit']  =             $s02data['teaunit']  ;
        $data['meaunit']  =             $s02data['meaunit']  ;
        $data['lununit']  =             $s02data['lununit']  ;
        $data['dinunit']  =             $s02data['dinunit']  ;
        $data['sinunit']  =             $s02data['sinunit']  ;
        $data['doneunit'] =             $s02data['doneunit'] ;
        $data['dtwounit'] =             $s02data['dtwounit'] ;
        $data['meaunit']  =             $s02data['meaunit']  ;

        $data['tabtype']    ='';
        $data['buftype']    ='';
        $data['lovecnt']    = 0;
        $data['luncnt'] =  isset($data['luncnt'])? $data['luncnt']:0; 
        $data['otheramt'] =  isset($data['otheramt'])? $data['otheramt']:0;   
        $data['siteamt'] =  isset($data['siteamt'])? $data['siteamt']:0;     


        $data['lunvegan'] =  isset($data['lunvegan'])? $data['lunvegan']:0;   
        $data['meavegan'] =  isset($data['meavegan'])? $data['meavegan']:0;
        $data['meacnt']   =  isset($data['meacnt'])? $data['meacnt']:0;
        $data['dinvegan'] =  isset($data['dinvegan'])? $data['dinvegan']:0;
        $data['dincnt']   =  isset($data['dincnt'])? $data['dincnt']:0;
        $data['sincnt']   =  isset($data['sincnt'])? $data['sincnt']:0;
        $data['teacnt']   =  isset($data['teacnt'])? $data['teacnt']:0;         
        $data['donecnt']  =  isset($data['donecnt'])? $data['donecnt']:0;
        $data['dtwocnt']  =  isset($data['dtwocnt'])? $data['dtwocnt']:0;
        $data['tabcnt']   =  isset($data['tabcnt'])? $data['tabcnt']:0;
        $data['tabvegan'] =  isset($data['tabvegan'])? $data['tabvegan']:0;
        $data['tabunit']  =  isset($data['tabunit'])? $data['tabunit']:0;
        $data['bufcnt']   =  isset($data['bufcnt'])? $data['bufcnt']:0;
        $data['bufvegan'] =  isset($data['bufvegan'])? $data['bufvegan']:0;
        $data['teaunit']  =  isset($data['teaunit'])? $data['teaunit']:0;   
        $data['bufunit']  = isset($data['bufunit'])?$data['bufunit']:0;
        $data['teatime']  = isset($data['teatime'])?$data['teatime']:''; 


        DB::beginTransaction();
        try{
            // if(!in_array($live['date'],$olddaylist) && $live['extradorm'] > 0){
            if(!in_array($live['date'],$olddaylist)){

                $data['class']      =$data['groupclass'];
                $data['term']       =$data['groupterm'];

                $data['date']    = $live['date'];
                $data['sincnt']  = $live['extradorm'];
                $data['request'] = $live['request'];
                $data['affirm']  = $live['affirm'];

                //提前住宿
      
                //新增的時候同時作業兩筆  3 / 5
                $data['type'] = 5; 
                T23tb::create($data);
                
                $data['type'] = 3; 
                T23tb::create($data);

                $extradorm = 1;
            }else{
                $extradorm = 0;
            }
            //課中住宿
            $i=1;
            foreach ($daylist as $key => $value) {
                // 日期不可重複
                if(in_array($value['date'],$olddaylist)){
                    $i++;
                    continue;
                }
                // '檢查開課日期是否已過確認凍結日
                $newdate = (substr($value['date'], 0,-4)+1911).substr($value['date'], -4);
                $affirmVids = date('Ymd',strtotime($newdate .' -'.(date('w',strtotime($newdate))+8-$s02data['weekly']).'Day')); //確認凍結日
                $requestVids = date('Ym',strtotime($newdate .' -1 month')).$s02data['monthly'];  //需求凍結日

                $data['request'] = (date('Y',strtotime($requestVids))-1911).date('md',strtotime($requestVids)); //需求凍結日
                $data['affirm'] = (date('Y',strtotime($affirmVids))-1911).date('md',strtotime($affirmVids)); //確認凍結日

                if($i==1){ //第一筆


                    $data['class']      =$data['groupclass'];
                    $data['term']       =$data['groupterm'];
                    $data['date']       =$value['date'];
                    $data['sincnt']     =$live['dincnt'];
                    $data['meacnt']     =$live['extradorm'];
                    $data['meavegan']   =$live['extradorm_vegan'];
                    $data['luncnt']     =$live['luncnt'];
                    $data['lunvegan']   =$live['vegan'];
                    $data['dincnt']     =$live['dincnt'];
                    $data['dinvegan']   =$live['dorm_vegan'];
                    $data['request']    =$live['request'];
                    $data['affirm']     =$live['affirm'];
                    $data['tabtype']    ='';
                    $data['buftype']    ='';


                    $data['luncnt'] =  isset($data['luncnt'])? $data['luncnt']:0; 
                    $data['otheramt'] =  isset($data['otheramt'])? $data['otheramt']:0;   
                    $data['siteamt'] =  isset($data['siteamt'])? $data['siteamt']:0;     

                    $data['lunvegan'] =  isset($data['lunvegan'])? $data['lunvegan']:0;   
                    $data['meavegan'] =  isset($data['meavegan'])? $data['meavegan']:0;
                    $data['meacnt']   =  isset($data['meacnt'])? $data['meacnt']:0;
                    $data['dinvegan'] =  isset($data['dinvegan'])? $data['dinvegan']:0;
                    $data['dincnt']   =  isset($data['dincnt'])? $data['dincnt']:0;
                    $data['sincnt']   =  isset($data['sincnt'])? $data['sincnt']:0;
                    $data['teacnt']   =  isset($data['teacnt'])? $data['teacnt']:0;         
                    $data['donecnt']  =  isset($data['donecnt'])? $data['donecnt']:0;
                    $data['dtwocnt']  =  isset($data['dtwocnt'])? $data['dtwocnt']:0;
                    $data['tabcnt']   =  isset($data['tabcnt'])? $data['tabcnt']:0;
                    $data['tabvegan'] =  isset($data['tabvegan'])? $data['tabvegan']:0;
                    $data['tabunit']  =  isset($data['tabunit'])? $data['tabunit']:0;
                    $data['bufcnt']   =  isset($data['bufcnt'])? $data['bufcnt']:0;
                    $data['bufvegan'] =  isset($data['bufvegan'])? $data['bufvegan']:0;
                    $data['teaunit']  =  isset($data['teaunit'])? $data['teaunit']:0;   
                    $data['bufunit']  =  isset($data['bufunit'])?$data['bufunit']:0;
                    $data['teatime']  =  isset($data['teatime'])?$data['teatime']:''; 

               
                    //新增的時候同時作業兩筆  3 / 5
                    $data['type'] = 5; 
                    T23tb::create($data);
                    
                    $data['type'] = 3; 
                    T23tb::create($data);

                }elseif($i == count($daylist)){ //最後一筆

                    $data['class']     =$data['groupclass'];
                    $data['term']       =$data['groupterm'];
                    $data['date']       =$value['date'];
                    $data['meacnt']     =$live['dincnt'];
                    $data['meavegan']   =$live['dorm_vegan'];
                    $data['luncnt']     =$live['luncnt'];
                    $data['lunvegan']   =$live['vegan'];
                    $data['request']    =$live['request'];
                    $data['affirm']     =$live['affirm'];
                    $data['tabtype']    ='';
                    $data['buftype']    ='';

                    $data['luncnt'] =  isset($data['luncnt'])? $data['luncnt']:0; 
                    $data['otheramt'] =  isset($data['otheramt'])? $data['otheramt']:0;   
                    $data['siteamt'] =  isset($data['siteamt'])? $data['siteamt']:0;     

                    $data['lunvegan'] =  isset($data['lunvegan'])? $data['lunvegan']:0;   
                    $data['meavegan'] =  isset($data['meavegan'])? $data['meavegan']:0;
                    $data['meacnt']   =  isset($data['meacnt'])? $data['meacnt']:0;
                    $data['dinvegan'] =  isset($data['dinvegan'])? $data['dinvegan']:0;
                    $data['dincnt']   =  isset($data['dincnt'])? $data['dincnt']:0;
                    $data['sincnt']   =  isset($data['sincnt'])? $data['sincnt']:0;
                    $data['teacnt']   =  isset($data['teacnt'])? $data['teacnt']:0;         
                    $data['donecnt']  =  isset($data['donecnt'])? $data['donecnt']:0;
                    $data['dtwocnt']  =  isset($data['dtwocnt'])? $data['dtwocnt']:0;
                    $data['tabcnt']   =  isset($data['tabcnt'])? $data['tabcnt']:0;
                    $data['tabvegan'] =  isset($data['tabvegan'])? $data['tabvegan']:0;
                    $data['tabunit']  =  isset($data['tabunit'])? $data['tabunit']:0;
                    $data['bufcnt']   =  isset($data['bufcnt'])? $data['bufcnt']:0;
                    $data['bufvegan'] =  isset($data['bufvegan'])? $data['bufvegan']:0;
                    $data['teaunit']  =  isset($data['teaunit'])? $data['teaunit']:0;   
                    $data['bufunit']  = isset($data['bufunit'])?$data['bufunit']:0;
                    $data['teatime']  = isset($data['teatime'])?$data['teatime']:''; 
                    //新增的時候同時作業兩筆  3 / 5
                    $data['type'] = 5; 
                    T23tb::create($data);
                    
                    $data['type'] = 3; 
                    T23tb::create($data);
                    $extradorm++;
                    break;
                }else{
                 
                    $data['class'] =$data['groupclass'];
                    $data['term']  =$data['groupterm'];
                    $data['date']  =$value['date'];
                    $data['sincnt']    =$live['dincnt'];
                    $data['meacnt']    =$live['dincnt'];
                    $data['meavegan']  =$live['dorm_vegan'];
                    $data['luncnt']    =$live['luncnt'];
                    $data['lunvegan']  =$live['vegan'];
                    $data['dincnt']    =$live['dincnt'];
                    $data['dinvegan']  =$live['dorm_vegan'];
                    $data['request']   =$live['request'];
                    $data['affirm']    =$live['affirm'];
                    $data['tabtype']   ='';
                    $data['buftype']   ='';

                    $data['luncnt'] =  isset($data['luncnt'])? $data['luncnt']:0; 
                    $data['otheramt'] =  isset($data['otheramt'])? $data['otheramt']:0;   
                    $data['siteamt'] =  isset($data['siteamt'])? $data['siteamt']:0;     
                    $data['lunvegan'] =  isset($data['lunvegan'])? $data['lunvegan']:0;   
                    $data['meavegan'] =  isset($data['meavegan'])? $data['meavegan']:0;
                    $data['meacnt']   =  isset($data['meacnt'])? $data['meacnt']:0;
                    $data['dinvegan'] =  isset($data['dinvegan'])? $data['dinvegan']:0;
                    $data['dincnt']   =  isset($data['dincnt'])? $data['dincnt']:0;
                    $data['sincnt']   =  isset($data['sincnt'])? $data['sincnt']:0;
                    $data['teacnt']   =  isset($data['teacnt'])? $data['teacnt']:0;         
                    $data['donecnt']  =  isset($data['donecnt'])? $data['donecnt']:0;
                    $data['dtwocnt']  =  isset($data['dtwocnt'])? $data['dtwocnt']:0;
                    $data['tabcnt']   =  isset($data['tabcnt'])? $data['tabcnt']:0;
                    $data['tabvegan'] =  isset($data['tabvegan'])? $data['tabvegan']:0;
                    $data['tabunit']  =  isset($data['tabunit'])? $data['tabunit']:0;
                    $data['bufcnt']   =  isset($data['bufcnt'])? $data['bufcnt']:0;
                    $data['bufvegan'] =  isset($data['bufvegan'])? $data['bufvegan']:0;
                    $data['teaunit']  =  isset($data['teaunit'])? $data['teaunit']:0;   
                    $data['bufunit']  = isset($data['bufunit'])?$data['bufunit']:0;
                    $data['teatime']  = isset($data['teatime'])?$data['teatime']:''; 

                    //新增的時候同時作業兩筆  3 / 5
                    $data['type'] = 5; 
                    T23tb::create($data);
                    
                    $data['type'] = 3; 
                    T23tb::create($data);
                }
                $extradorm++;
                $i++;
            }
            // T04tb::where('class',$data['groupclass'])->where('term',$data['groupterm'])->update(array('kind'=>$data['groupacccode']));
            $content ='批次新增(週確認) '.$extradorm.'筆辦班資料 班別/會議：'.$data['groupclass'].' 期別/編號：'.$data['groupterm'].' 開始日期：'.$day['sdate'].' 結束日期：'.$day['edate'];
            $logarray = $this->getlog('B',$content);
            //INSERT INTO t35tb ""
            T35tb::create($logarray);
            DB::commit();
            return back()->with('result', '1')->with('message', '更新成功，新增'.$extradorm.'筆資料!');
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message', '新增失敗!!');
        }
    }
    // 更新人數
    public function groupupdate($class_term){

        $data['groupclass'] = $class = substr($class_term, 0,-2);
        $data['groupterm'] = $term = substr($class_term, -2);

        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('classes_requirements', $class, $term);
        if($this->authority==1){
        
        }elseif($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法更新');
        }

        $classtype = preg_match("/^[A-z]/",$class );  // 判斷是否為會議 0:班別 1:會議
        $now = (date('Y',strtotime('now'))-1911).date('md',strtotime('now'));
        // 判斷有無預約場地
        $daylist = T22tb::select('date')->where('class',$class)->where('term',$term)->groupby('date')->get()->toarray();
        if($classtype=='1' && empty($daylist)){
            $t38day = T38tb::select('sdate','edate')->where('meet',$class)->where('serno',$term)->groupby('date')->get()->toarray();
            $sdate = (substr($t38day['sdate'], 0,-4)+1911).substr($t38day['sdate'], -4);
            $edate = (substr($t38day['edate'], 0,-4)+1911).substr($t38day['edate'], -4);
            $daylist = array();
            while (true) {
                $daylist[] = array('date'=>(substr($sdate, 0,-4)-1911).substr($sdate, -4));
                if($sdate == $edate){
                    break;
                }
                $sdate = date('Ymd',strtotime($sdate .' +1 Day'));
                if($i>365){
                    return back()->with('result', '0')->with('message', 'error!');
                    break;
                }
                $i++;
            }
        }elseif($classtype=='0' && empty($daylist)){
            $daylist = T36tb::select('date')->where('class',$class)->where('term',$term)->groupby('date')->get()->toarray();
        }
        if(empty($daylist))  return back()->with('result', '0')->with('message', '找不到班級或會議的資料，無法批次新增!');

        // if(end($daylist)['date']< $now) return back()->with('result', '0')->with('message', '沒有可更新的資料!');


        $live = $this->classesrequirementsService->getLiveList($data);
        $day = T04tb::select('sdate','edate')->where('class',$class)->where('term',$term)->first();

         // $gettimes = T22tb::select('request','affirm')->where('class',$data['groupclass'])->where('term',$data['groupterm'])->first();
        $s02data = S02tb::select('*')->first(); /*凍結日(週、月)(2、05) */
        $newdate = (substr($day['sdate'], 0,-4)+1911).substr($day['sdate'], -4);
        $advance = (date('Y',strtotime($newdate .' -1 Day'))).date('md',strtotime($newdate .' -1 Day'));
        $affirmVids = date('Ymd',strtotime($advance .' -'.(date('w',strtotime($advance))+8-$s02data['weekly']).'Day')); //確認凍結日
        $requestVids = date('Ym',strtotime($advance .' -1 month')).$s02data['monthly'];  //需求凍結日
        $live['request'] = (date('Y',strtotime($requestVids))-1911).date('md',strtotime($requestVids)); //需求凍結日
        $live['affirm'] = (date('Y',strtotime($affirmVids))-1911).date('md',strtotime($affirmVids)); //確認凍結日
        $live['date'] = (date('Y',strtotime($advance))-1911).date('md',strtotime($advance));


        $data['upddate']  =              date('Y-m-d H:i:s');
        $data['teaunit']  =             $s02data['teaunit']  ;
        $data['meaunit']  =             $s02data['meaunit']  ;
        $data['lununit']  =             $s02data['lununit']  ;
        $data['dinunit']  =             $s02data['dinunit']  ;
        $data['sinunit']  =             $s02data['sinunit']  ;
        $data['doneunit'] =             $s02data['doneunit'] ;
        $data['dtwounit'] =             $s02data['dtwounit'] ;
        $data['meaunit']  =             $s02data['meaunit']  ;

    

        $data['lovecnt']    =0;
        $data['luncnt'] =  isset($data['luncnt'])? $data['luncnt']:0; 
        $data['otheramt'] =  isset($data['otheramt'])? $data['otheramt']:0;   
        $data['siteamt'] =  isset($data['siteamt'])? $data['siteamt']:0;     
        $data['lunvegan'] =  isset($data['lunvegan'])? $data['lunvegan']:0;   
        $data['meavegan'] =  isset($data['meavegan'])? $data['meavegan']:0;
        $data['meacnt']   =  isset($data['meacnt'])? $data['meacnt']:0;
        $data['dinvegan'] =  isset($data['dinvegan'])? $data['dinvegan']:0;
        $data['dincnt']   =  isset($data['dincnt'])? $data['dincnt']:0;
        $data['sincnt']   =  isset($data['sincnt'])? $data['sincnt']:0;
        $data['teacnt']   =  isset($data['teacnt'])? $data['teacnt']:0;         
        $data['donecnt']  =  isset($data['donecnt'])? $data['donecnt']:0;
        $data['dtwocnt']  =  isset($data['dtwocnt'])? $data['dtwocnt']:0;
        $data['tabcnt']   =  isset($data['tabcnt'])? $data['tabcnt']:0;
        $data['tabvegan'] =  isset($data['tabvegan'])? $data['tabvegan']:0;
        $data['tabunit']  =  isset($data['tabunit'])? $data['tabunit']:0;
        $data['bufcnt']   =  isset($data['bufcnt'])? $data['bufcnt']:0;
        $data['bufvegan'] =  isset($data['bufvegan'])? $data['bufvegan']:0;
        $data['teaunit']  =  isset($data['teaunit'])? $data['teaunit']:0;   
        $data['bufunit']  = isset($data['bufunit'])?$data['bufunit']:0;
        $data['teatime']  = isset($data['teatime'])?$data['teatime']:'';    


        DB::beginTransaction();
        try{
            // 刪除舊資料
            T23tb::where('class',$class)->where('term',$term)->where('date','>=',$now)->where('type','5')->delete();
            $t23data = T23tb::select('date')->where('class',$class)->where('term',$term)->where('type','5')->groupby('date')->get()->toarray();
            $olddaylist = array();
            foreach ($t23data as $key => $value) {
                $olddaylist[] = $value['date'];
            }
            if(!in_array($live['date'],$olddaylist) && $live['extradorm'] > 0){
                //提前住宿
            
                $data['class']     =$class;
                $data['term']      =$term;
                $data['date']      =$live['date'];
                $data['sincnt']    =$live['extradorm'];
                $data['request']   =$live['request'];
                $data['affirm']    =$live['affirm'];

                //新增的時候同時作業兩筆  3 / 5
                $data['type'] = 5; 
                T23tb::create($data);
                
                $data['type'] = 3; 
                T23tb::create($data);

                $extradorm = 1;
            }else{
                $extradorm = 0;
            }
            //課中住宿
            $i=1;
            foreach ($daylist as $key => $value) {
                // 日期不可重複
                if(in_array($value['date'],$olddaylist)){
                    $i++;
                    continue;
                }
                // '檢查開課日期是否已過確認凍結日
                $newdate = (substr($value['date'], 0,-4)+1911).substr($value['date'], -4);
                $affirmVids = date('Ymd',strtotime($newdate .' -'.(date('w',strtotime($newdate))+8-$s02data['weekly']).'Day')); //確認凍結日
                $requestVids = date('Ym',strtotime($newdate .' -1 month')).$s02data['monthly'];  //需求凍結日

                $data['request'] = (date('Y',strtotime($requestVids))-1911).date('md',strtotime($requestVids)); //需求凍結日
                $data['affirm'] = (date('Y',strtotime($affirmVids))-1911).date('md',strtotime($affirmVids)); //確認凍結日

                if($i==1){ //第一筆
              
                        $data['class'] =$class;
                        $data['term']  =$term;
                        $data['date']  =$value['date'];
                        $data['sincnt']    =$live['extradorm'];
                        $data['meacnt']    =$live['extradorm'];
                        $data['meavegan']  =$live['extradorm_vegan'];
                        $data['luncnt']    =$live['luncnt'];
                        $data['lunvegan']  =$live['vegan'];
                        $data['dincnt']    =$live['dincnt'];
                        $data['dinvegan']  =$live['dorm_vegan'];
                        $data['request']   =$live['request'];
                        $data['affirm']    =$live['affirm'];
                        $data['tabtype']   ='';
                        $data['buftype']   ='';
                        $data['lovecnt']    =0;
                        $data['luncnt'] =  isset($data['luncnt'])? $data['luncnt']:0; 
                        $data['otheramt'] =  isset($data['otheramt'])? $data['otheramt']:0;   
                        $data['siteamt'] =  isset($data['siteamt'])? $data['siteamt']:0;     
                        $data['lunvegan'] =  isset($data['lunvegan'])? $data['lunvegan']:0;   
                        $data['meavegan'] =  isset($data['meavegan'])? $data['meavegan']:0;
                        $data['meacnt']   =  isset($data['meacnt'])? $data['meacnt']:0;
                        $data['dinvegan'] =  isset($data['dinvegan'])? $data['dinvegan']:0;
                        $data['dincnt']   =  isset($data['dincnt'])? $data['dincnt']:0;
                        $data['sincnt']   =  isset($data['sincnt'])? $data['sincnt']:0;
                        $data['teacnt']   =  isset($data['teacnt'])? $data['teacnt']:0;         
                        $data['donecnt']  =  isset($data['donecnt'])? $data['donecnt']:0;
                        $data['dtwocnt']  =  isset($data['dtwocnt'])? $data['dtwocnt']:0;
                        $data['tabcnt']   =  isset($data['tabcnt'])? $data['tabcnt']:0;
                        $data['tabvegan'] =  isset($data['tabvegan'])? $data['tabvegan']:0;
                        $data['tabunit']  =  isset($data['tabunit'])? $data['tabunit']:0;
                        $data['bufcnt']   =  isset($data['bufcnt'])? $data['bufcnt']:0;
                        $data['bufvegan'] =  isset($data['bufvegan'])? $data['bufvegan']:0;
                        $data['teaunit']  =  isset($data['teaunit'])? $data['teaunit']:0;   
                        $data['bufunit']  = isset($data['bufunit'])?$data['bufunit']:0;
                        $data['teatime']  = isset($data['teatime'])?$data['teatime']:''; 

                        //新增的時候同時作業兩筆  3 / 5
                        $data['type'] = 5; 
                        T23tb::create($data);
                        
                        $data['type'] = 3; 
                        T23tb::create($data);

                }elseif($i == count($daylist)){ //最後一筆
                
                        $data['class'] = $class;
                        $data['term'] = $term;
                        $data['date'] = $value['date'];
                        $data['meacnt'] = $live['dincnt'];
                        $data['meavegan'] = $live['dorm_vegan'];
                        $data['luncnt'] = $live['luncnt'];
                        $data['lunvegan'] = $live['vegan'];
                        $data['request'] = $live['request'];
                        $data['affirm'] = $live['affirm'];
                        $data['tabtype'] = '';
                        $data['buftype'] = '';
                        $data['lovecnt']    =0;
                    $data['luncnt'] =  isset($data['luncnt'])? $data['luncnt']:0; 
                    $data['otheramt'] =  isset($data['otheramt'])? $data['otheramt']:0;   
                    $data['siteamt'] =  isset($data['siteamt'])? $data['siteamt']:0;     
                    $data['lunvegan'] =  isset($data['lunvegan'])? $data['lunvegan']:0;   
                    $data['meavegan'] =  isset($data['meavegan'])? $data['meavegan']:0;
                    $data['meacnt']   =  isset($data['meacnt'])? $data['meacnt']:0;
                    $data['dinvegan'] =  isset($data['dinvegan'])? $data['dinvegan']:0;
                    $data['dincnt']   =  isset($data['dincnt'])? $data['dincnt']:0;
                    $data['sincnt']   =  isset($data['sincnt'])? $data['sincnt']:0;
                    $data['teacnt']   =  isset($data['teacnt'])? $data['teacnt']:0;         
                    $data['donecnt']  =  isset($data['donecnt'])? $data['donecnt']:0;
                    $data['dtwocnt']  =  isset($data['dtwocnt'])? $data['dtwocnt']:0;
                    $data['tabcnt']   =  isset($data['tabcnt'])? $data['tabcnt']:0;
                    $data['tabvegan'] =  isset($data['tabvegan'])? $data['tabvegan']:0;
                    $data['tabunit']  =  isset($data['tabunit'])? $data['tabunit']:0;
                    $data['bufcnt']   =  isset($data['bufcnt'])? $data['bufcnt']:0;
                    $data['bufvegan'] =  isset($data['bufvegan'])? $data['bufvegan']:0;
                    $data['teaunit']  =  isset($data['teaunit'])? $data['teaunit']:0;   
                    $data['bufunit']  = isset($data['bufunit'])?$data['bufunit']:0;
                    $data['teatime']  = isset($data['teatime'])?$data['teatime']:''; 
                        //新增的時候同時作業兩筆  3 / 5
                        $data['type'] = 5; 
                        T23tb::create($data);
                        
                        $data['type'] = 3; 
                        T23tb::create($data);
                    $extradorm++;
                    break;
                }else{
                 
                         $data['class'] =$class;
                         $data['term']  =$term;
                         $data['date']  =$value['date'];
                         $data['sincnt']    =$live['dincnt'];
                         $data['meacnt']    =$live['dincnt'];
                         $data['meavegan']  =$live['dorm_vegan'];
                         $data['luncnt']    =$live['luncnt'];
                         $data['lunvegan']  =$live['vegan'];
                         $data['dincnt']    =$live['dincnt'];
                         $data['dinvegan']  =$live['dorm_vegan'];
                         $data['request']   =$live['request'];
                         $data['affirm']    =$live['affirm'];
                         $data['tabtype']   ='';
                         $data['buftype']   ='';
                         $data['lovecnt']    =0;
                         $data['luncnt'] =  isset($data['luncnt'])? $data['luncnt']:0; 
                         $data['otheramt'] =  isset($data['otheramt'])? $data['otheramt']:0;   
                         $data['siteamt'] =  isset($data['siteamt'])? $data['siteamt']:0;     
                         $data['lunvegan'] =  isset($data['lunvegan'])? $data['lunvegan']:0;   
                         $data['meavegan'] =  isset($data['meavegan'])? $data['meavegan']:0;
                         $data['meacnt']   =  isset($data['meacnt'])? $data['meacnt']:0;
                         $data['dinvegan'] =  isset($data['dinvegan'])? $data['dinvegan']:0;
                         $data['dincnt']   =  isset($data['dincnt'])? $data['dincnt']:0;
                         $data['sincnt']   =  isset($data['sincnt'])? $data['sincnt']:0;
                         $data['teacnt']   =  isset($data['teacnt'])? $data['teacnt']:0;         
                         $data['donecnt']  =  isset($data['donecnt'])? $data['donecnt']:0;
                         $data['dtwocnt']  =  isset($data['dtwocnt'])? $data['dtwocnt']:0;
                         $data['tabcnt']   =  isset($data['tabcnt'])? $data['tabcnt']:0;
                         $data['tabvegan'] =  isset($data['tabvegan'])? $data['tabvegan']:0;
                         $data['tabunit']  =  isset($data['tabunit'])? $data['tabunit']:0;
                         $data['bufcnt']   =  isset($data['bufcnt'])? $data['bufcnt']:0;
                         $data['bufvegan'] =  isset($data['bufvegan'])? $data['bufvegan']:0;
                         $data['teaunit']  =  isset($data['teaunit'])? $data['teaunit']:0;   
                         $data['bufunit']  = isset($data['bufunit'])?$data['bufunit']:0;
                         $data['teatime']  = isset($data['teatime'])?$data['teatime']:''; 

                         //新增的時候同時作業兩筆  3 / 5
                        $data['type'] = 5; 
                        T23tb::create($data);
                        
                        $data['type'] = 3; 
                        T23tb::create($data);
                }
                $extradorm++;
                $i++;
            }
            // T04tb::where('class',$class)->where('term',$term)->update(array('kind'=>$data['groupacccode']));
            $content ='更新人數 '.$extradorm.'筆辦班資料 班別/會議：'.$class.' 期別/編號：'.$term.' 開始日期：'.$day['sdate'].' 結束日期：'.$day['edate'];
            $logarray = $this->getlog('B',$content);
            //INSERT INTO t35tb ""
            T35tb::create($logarray);
            DB::commit();
            return back()->with('result', '1')->with('message', '更新成功');
            // return back()->with('result', '1')->with('message', '更新成功，更新'.$extradorm.'筆資料!');
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message', '更新失敗!!');
        }
    }
    //批次刪除
    public function groupdestroy($class_term){

        $term = $queryData['term'] = substr($class_term, -2);
        $class = $queryData['class'] = substr($class_term, 0,-2);

        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('classes_requirements', $class, $term);
        if($this->authority==1){
        
        }elseif($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法刪除');
        }

        if(!$class_term) return back()->with('result', '1')->with('message', '錯誤!請選重新整理頁面');

        $content ='批次刪除辦班資料 班別/會議：'.$queryData['class'].' 期別/編號：'.$queryData['term'];
        $logarray = $this->getlog('D',$content);
        DB::beginTransaction();
        try{
            T35tb::create($logarray);
            T23tb::where('class',$queryData['class'])->where('term',$queryData['term'])->delete();
            DB::commit();
            return redirect('/admin/classes_requirements/edit/'.$class.$term)->with('result', '1')->with('message', '批次刪除成功!');
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message', '批次刪除失敗!!');
        }
    }

    private function getlog($type,$content=NULL,$rule=[]){
        $logarray['logdate'] = (date('Y')-1911).date('md');
        $ns = substr(microtime(),2,4);
        $logarray['logtime'] = date('H:i:s:').$ns;
        $logarray['userid'] = Auth::guard('managers')->user()->userid;
        $logarray['progid'] = isset($rule['progid'])? $rule['progid']:'CSDI6030';
        $logarray['type'] = $type;
        $logarray['logtable'] = isset($rule['logtable'])? $rule['logtable']:'t23tb';
        if(!is_null($content)){
            $logarray['content'] = $content;
        }
        return $logarray;
    }

    public function _get_year_list() {
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