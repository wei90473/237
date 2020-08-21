<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ClassesService;
use App\Services\User_groupService;
use App\Models\Classes;
use App\Models\Class_group;
use App\Models\T01tb;
use App\Models\S03tb;
use App\Models\M17tb;
use App\Models\M09tb;
use App\Helpers\ModifyLog;
use DB;

class ClassesController extends Controller
{
	// public $progid = 'classes';
    /**
     * ClassesController constructor.
     * @param ClassesService $classesService
     */
    public function __construct(ClassesService $classesService, User_groupService $user_groupService)
    {
        $this->classesService = $classesService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('classes', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
            // dd($user_data);
            // dd(\session());
        });
        setProgid('classes');
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
        // 班號
        $queryData['class'] = $request->get('class');
        // 辦班院區
        $queryData['branch'] = $request->get('branch');
        // 班別名稱
        $queryData['name'] = $request->get('name');
        // 分班名稱
        $queryData['branchname'] = $request->get('branchname');
        // 班別類型
        $queryData['process'] = $request->get('process');
        // 委訓單位**
        $queryData['commission'] = $request->get('commission');
        // 訓練性質
        $queryData['traintype'] = $request->get('traintype');
        // 班別性質
        $queryData['type'] = $request->get('type');
        // 類別1
        $queryData['categoryone'] = $request->get('categoryone');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 取得列表資料
        if(empty($request->all())) {
            $queryData['choices'] = $this->_get_year_list();
            return view('admin/classes/list', compact('queryData'));
        }

        $data = $this->classesService->getClassesList($queryData);
        $ranklist = $this->classesService->getClassesList(array('yerly'=>$queryData['yerly'],'_paginate_qty'=>'999'));
        $queryData['choices'] = $this->_get_year_list();
        return view('admin/classes/list', compact('data', 'queryData','ranklist'));
    }

    /**
     * 新增頁
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        // 取得班別類別
        $classCategory = $this->getClassCategory();

        $orgchkPopList = $this->getOrgchk(null, 'new', 'xxxxxx');

        $sameCourseList = Class_group::select('groupid','class_group')->groupby('groupid')->get()->toarray();

        return view('admin/classes/form', compact('classCategory', 'orgchkPopList','sameCourseList'));
    }

    /**
     * 批次增刪班別頁
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function batch()
    {
        $queryData['choices'] = $this->_get_year_list();
        return view('admin/classes/batch', compact('queryData'));
    }
    /*匯入*/
    public function batchimport(Request $request){
        $csv_file = $request->file('csv_file');
        if(!isset($csv_file)) return redirect('/admin/classes/batch')->with('result', '0')->with('message', '匯入失敗，沒有任何資料！'); //匯入新增

        $ext = $csv_file->getClientOriginalExtension();  //取得上傳檔案副檔名
        if($ext!=="csv")  return redirect('/admin/classes/batch')->with('result', '0')->with('message', '請選擇要匯入的CSV檔案！'); //只收CSV

        $handle = fopen($csv_file, 'r');
        $result = $this->input_csv($handle); //解析csv
        $len_result = count($result);
        if(count($result)==0)  return redirect('/admin/classes/batch')->with('result', '0')->with('message', '匯入失敗，沒有任何資料！');

        // if(count($result[0]) != 67) return redirect('/admin/classes/batch')->with('result', '0')->with('message', '匯入失敗，格式錯誤！');

        $falsrtimes = 0;
        $successtimes = 0;
        $errorclass = '';
        $dblist = array('class','name','type','style','post','process','traintype','board','kind','period','quota','dayhour','publish','time1','time2','time3','time4','time5','time6','time7','holiday','trainday','category','upload1','classified','elearnhr','classhr','cntflag','signin','chfchk','perchk','rkchk','subchfchk','subperchk','subrkchk','orgchk','english','remark','target','object','content','profchk','branch','areachk','branchname','commission','categoryone','precautions','signupquota','signupexquota','registration');                  
        
        foreach ($result as $class => $classdata) {
            $errormsg ='';
            $createdata = array();
            if($class==0) {
                foreach ($classdata as $k => $v) {
                    $key = $dblist[$k];
                    $createdata[$key] = mb_convert_encoding($v,"UTF-8", "BIG5");
                }
                if(sizeof($classdata) != 51) {
                    return redirect('/admin/classes/batch')->with('result', '0')->with('message', '匯入失敗，資料格式錯誤請匯出CSV來進行編輯！');
                }
                $size = sizeof($classdata);
                $list = $createdata;
                continue;
                
            }else{
                $createdata = array('classified'=>'','process'=>'');
                for($i=0;$i<$size;$i++){
                    $key = $dblist[$i];
                    // if(in_array($key, array('name','remark','target','object','content'))){
                    // $v = mb_convert_encoding($classdata[$i],"UTF-8", "BIG5");
                    // }
                    $createdata[$key] = mb_convert_encoding($classdata[$i],"UTF-8", "BIG5");
                    // 必填判斷
                    if(in_array($key, array('class','name','type','style','post','process','traintype','board','kind','period','quota','dayhour','publish','trainday','category','upload1','classified','cntflag','signin','orgchk','profchk','branch','areachk','categoryone','signupquota','signupexquota','registration')) && $classdata[$i]=''){
                        $errormsg .=$list[$key].'為必填欄位。' ;
                    }elseif($key=='classhr' && $createdata['classified']=='2' && $classdata[$i]=='' ){
                        $errormsg .=$list[$key].'為必填欄位。' ;
                    }elseif($key=='elearnhr' && $createdata['classified']=='1' && $classdata[$i]=='' ){
                        $errormsg .=$list[$key].'為必填欄位。' ;
                    }elseif($key=='commission' && $createdata['process']=='2' && $classdata[$i]=='' ){
                        $errormsg .=$list[$key].'為必填欄位。' ;
                    }
                }
            }
            // 排除異常class
            if(!preg_match("/^([0-9A-Za-z]+)$/" ,$createdata['class'])){
                continue;
            }
            
            // 資料優化與檢核
            if($createdata['branch']=='1'){
                $createdata['branchcode'] = 'A';
            }elseif($createdata['branch']=='2'){
                $createdata['branchcode'] = 'B';
            }else{
                $createdata['branchcode'] = '';
                $errormsg .='辦班院區錯誤。' ;    
            }
            if(strlen($createdata['class'])<6 || strlen($createdata['class'])>8){
                $errormsg .='班別格式錯誤。' ;
            }else{
                if(strlen($createdata['class'])==6){
                    $createdata['class'] = $createdata['class'].$createdata['branchcode'];    
                }
                // if(substr($createdata['class'], -1) =='A'){
                //     $createdata['branch'] = '1';
                // }else{
                //     $createdata['branch'] = '2';
                // }
                // $createdata['branchcode'] = substr($createdata['class'], -1);
            }

            $createdata['day'] = $createdata['trainday'];
            $createdata['extraquota'] = '0';
            $createdata['rank'] = '1';
            $createdata['elearnhr'] = is_null($createdata['elearnhr'])?$createdata['elearnhr']:'0';
            $createdata['classhr'] = is_null($createdata['classhr'])?$createdata['classhr']:'0';
            $createdata['trainhour'] = $createdata['elearnhr']+$createdata['classhr'];
            $createdata['special'] = 'N';
            $createdata['yerly'] = substr($createdata['class'], 0,3);
            $createdata['trace'] = 'N';
            $createdata['teaching'] = 'N';
            // **上課方式天數
            if($createdata['style']==4){
                $createdata['time1'] = is_null($createdata['time1'])?$createdata['time1']:'N';
                $createdata['time2'] = is_null($createdata['time2'])?$createdata['time2']:'N';
                $createdata['time3'] = is_null($createdata['time3'])?$createdata['time3']:'N';
                $createdata['time4'] = is_null($createdata['time4'])?$createdata['time4']:'N';
                $createdata['time5'] = is_null($createdata['time5'])?$createdata['time5']:'N';
                $createdata['time6'] = is_null($createdata['time6'])?$createdata['time6']:'N';
                $createdata['time7'] = is_null($createdata['time7'])?$createdata['time7']:'N';
                $createdata['holiday'] = is_null($createdata['holiday'])?$createdata['holiday']:'N';
            }
            $createdata['rkchk'] = str_replace(array("|"), ',',$createdata['rkchk']);
            $createdata['subrkchk'] = str_replace(array("|"), ',',$createdata['subrkchk']);
            $createdata['modifyuser'] = \Auth::user()->userid;
            $createdata['modifytime'] = date('Y-m-d H:i:s');
            $check = t01tb::where('class',$createdata['class'])->count();
            if($check>0 ){
                $errormsg .='該班別已存在。';
            }
            // var_dump($createdata);exit();
            // 寫入
            if(strlen($errormsg)>1) {
                $errorclass .=  $createdata['class'].'：'.$errormsg.'；';
                $falsrtimes++;
            }else{
                DB::connection()->enableQueryLog(); //啟動SQL_LOG
                $successtimes++;
                T01tb::create($createdata);
                $nowdata = T01tb::where('class',$createdata['class'])->get()->toArray();
	            $sql = DB::getQueryLog();
	            createModifyLog('I','class_group',NULL,$nowdata,end($sql));
            }
        }
        fclose($handle); //關閉指標
        if(($falsrtimes)>0) {
            return redirect('/admin/classes/batch')->with('result', '0')->with('message', '匯入成功'.$successtimes.'筆！失敗'.$falsrtimes.'筆!失敗班別'.$errorclass);
        }else{
            return redirect('/admin/classes/batch')->with('result', '1')->with('message', '匯入成功'.$successtimes.'筆！');
        }
    }
    /*新增*/
    public function batchadd(Request $request)
    {
        // 取得POST資料
        $data = $request->all();
        // 年度
        $year = $request->get('yerly');
        if(!isset($year)) redirect('/admin/classes/batch');

        $data = $this->classesService->getClassesdata($year);
        if($data) $this->__result('2','此年度有資料，無法使用此功能');

        $num = count($data);
        $data = $this->classesService->getClassesdata($year-1); //抓上一年度的資料
        if(!$data) $this->__result('2','上一年度無資料，無法使用此功能');

        $count = 0;
        DB::beginTransaction();
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
		try{
	         foreach ($data as $key => $value) {
	            $count = $count +1;
	            $value['yerly'] = $year;
                $value['times'] = '';
	            $value['class'] = $year.sprintf("%'03d", $count);
	            $this->setSamecourse($value);
                $value['modifyuser'] = \Auth::user()->userid;
                $value['modifytime'] = date('Y-m-d H:i:s');
                $result = T01tb::create($value);// 新增
                $sql = DB::getQueryLog();
                $nowdata = T01tb::where('class',$value['class'])->get()->toarray();
                unset($nowdata[0]['planmk']);
                createModifyLog('I','t01tb','',$nowdata,end($sql));
            }
            DB::commit();
            $this->__result('0','新增完畢!');
	     }catch ( Exception $e ){
            DB::rollback();
           $this->__result('2','新增失敗!');
        }
    }
    /*刪除*/
    public function batchdel(Request $request)
    {
        // 年度
        $year = $request->get('yerly');
        $data = $this->classesService->getClassesdata($year);
        $num = count($data);
        if($num==0) $this->__result('2','此年度無資料，無法使用此功能');

        DB::beginTransaction();
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
		try{
			$olddata = Class_group::where('class','like',$year.'%')->get()->toArray();
            Class_group::where('class','like',$year.'%')->delete();
            $sql = DB::getQueryLog();
            createModifyLog('D','class_group',$olddata,'',end($sql));
            $olddata = T01tb::where('yerly', $year)->get()->toArray();
            T01tb::where('yerly', $year)->delete();
            $sql = DB::getQueryLog();
            createModifyLog('D','t01tb',$olddata,'',end($sql));
            DB::commit();
            $this->__result('0','刪除成功!');
        }catch ( Exception $e ){
            DB::rollback();
           $this->__result('2','刪除失敗!');
        }

    }


    /*挑選匯出班別*/
    public function exportclass(Request $request)
    {
        // 年度
        $year = $request->get('yerly');
        $data = $this->classesService->getClassesdata($year);
        $num = count($data);
        if($num==0) $this->__result('2','此年度無資料，無法使用此功能');

        $results = array();
        $i=0;
        foreach ($data as $key => $value) {
            $results[$i]['class'] = $value['class'];
            $results[$i]['name'] = $value['name'];
            $i++;
        }
        return $results;
    }
    /*匯出*/
    public function ClassOutput(Request $request)
    {
        $year = $request->get('yerly');
        $export = $request->get('export');
        if(!is_null($export)){ //有挑選
            $basedata = $this->exportclass($request);
            $exportarr =array();
            foreach ($basedata as $key => $value) {
                if(substr($export, 0, 1)==1){
                    $exportarr[] = $value['class'];
                }
                $export = substr($export, 1);
            }

            $data = T01tb::whereIn('class', $exportarr)->get()->toArray();
        }else{//無條選
            $data = $this->classesService->getClassesdata($year);
            $num = count($data);
            if($num==0) return redirect('/admin/classes/batch')->with('result', '0')->with('message', '此年度無資料，無法使用此功能');

        }
        //設定瀏覽器讀取此份資料為不快取，與解讀行為是下載 CSV 檔案
        header("Pragma: no-cache");
        header("Expires: 0");
        header("Content-type: application/csv");
        //檔案名稱
        header("Content-Disposition: attachment; filename=".$year.iconv("UTF-8","big-5","年度資料").".csv");
        // $csv_arr[] = array('class','name','type','style','post','process','traintype','board','kind','period','day','quota','extraquota','dayhour','publish','rank','quotatot','time1','time2','time3','time4','time5','time6','time7','holiday','classtype','trainday','trainhour','special','category','is_must_read','upload1','classified','elearnhr','classhr','cntflag','signin','chfchk','perchk','rkchk','subchfchk','subperchk','subrkchk','orgchk','yerly','times','planmk','english','remark','target','object','content','trace','profchk','branch','areachk','teaching','chkclass','branchname','commission','categoryone','precautions','samecourse','signupquota','signupexquota','branchcode','newstyle','registration','class_process');
        

        $csv_arr[] = array('班號','班別名稱','班別性質','上課方式','官等區分','班別類型','訓練性質','是否住宿','訓期類型','訓期','每期人數','每日上課時數','網頁公告','週一上課','週二上課','週三上課','週四上課','週五上課','週六上課','週日上課','含國定假日','訓練總天數','班別類別','入口網站開班方式','學習性質','數位時數','實體時數','訓練績效計算方式','報名方式','第一組主管','第一組人事人員','第一組職等','第二組主管','第二組人事人員','第二組職等','參訓機關','英文班別名稱','備註','參加對象','研習目標','研習方式','講座審查','辦班院區','機關分區','分班名稱','委訓機關代碼','類別1','公告備註','正取名額','後補名額','開放報名對象');
        for($i=0;$i<sizeof($csv_arr[0]);$i++){
            $csv_arr[0][$i] = iconv("UTF-8","big-5",$csv_arr[0][$i]);
        }
        //輸出csv
        foreach ($data as $k => $v) {
            unset($v['day'],$v['extraquota'],$v['rank'],$v['quotatot'],$v['classtype'],$v['trainhour'],$v['special'],$v['is_must_read'],$v['yerly'],$v['times'],$v['planmk'],$v['trace'],$v['teaching'],$v['chkclass'],$v['samecourse'],$v['branchcode'],$v['newstyle'],$v['class_process']);
            foreach ($v as $key => $value) {
                $v[$key] = str_replace(array("\r", "\n", "\r\n", "\n\r"), '',$value);
                $v[$key] = iconv("UTF-8","big-5",$v[$key]);
            }
            $v['rkchk'] = str_replace(array(","), '|',$v['rkchk']);
            $v['subrkchk'] = str_replace(array(","), '|',$v['subrkchk']);
            // $v['planmk'] = ''; //文字編碼待排除***
            $csv_arr[] = $v;
            // if($v['class']=='108640') print_r($v['content']);exit();
        } 
        //正式循環輸出陣列內容
        for ($j = 0; $j < count($csv_arr); $j++) {
            if ($j == 0) {
                //檔案標頭如果沒補上 UTF-8 BOM 資訊的話，Excel 會解讀錯誤，偏向輸出給程式觀看的檔案
                // echo "\xEF\xBB\xBF";
            }
            //輸出符合規範的 CSV 字串以及斷行
            echo join(',', $csv_arr[$j]) . PHP_EOL;
            // echo $this->csvstr($csv_arr[$j]) . PHP_EOL;
        }
        exit;
    }
    /**排序調整**/
    public function rank(Request $request){
        $data = $request->all();
        unset($data['_token'],$data['_method']);
        if(count($data)>0){
        	DB::beginTransaction();
        	DB::connection()->enableQueryLog();
        	try {
        		foreach ($data as $key => $value) {
	                $olddata = t01tb::select('class','rank')->where('class',$key)->get()->toArray();
	                if($olddata[0]['rank'] != $value){
	                	t01tb::where('class',$key)->update(array('rank'=>$value,'modifyuser'=>\Auth::user()->userid,'modifytime'=>date('Y-m-d H:i:s')));
		                $sql = DB::getQueryLog();
		                $nowdata = t01tb::select('class','rank')->where('class',$key)->get()->toArray();
						createModifyLog('U','t01tb',$olddata,$nowdata,end($sql));
	                }
	            }
	            DB::commit();
	            return back()->with('result', '1')->with('message', '儲存成功!');
        	} catch (Exception $e) {
        		DB::rollback();
            	return back()->with('result', '0')->with('message', '儲存失敗，請稍後再試!');
        	}
        }else{
            return back()->with('result', '0')->with('message', '查無資料!');
        }
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
        if(strlen($data['class'])<6 )  return back()->with('result', '0')->with('message', '班號格式錯誤!');

        $check = T01tb::where('class',$data['class'].$data['branchcode'])->first();
        if(!is_null($check)) return back()->with('result', '0')->with('message', '新增失敗，班號重覆!');

        $data['class'] = $data['class'].$data['branchcode'];
        $data['yerly'] = substr($data['class'],0,3);
        if(isset($data['board'])){
        	$data['board'] =  ($data['board'] == '1')? 'Y':($data['board'] == '2')? 'X': 'N';
        }
        // 取得參訓機關(限定機關)資料
        $tempOrgchk = $data['tempOrgchk'];

        // 刪除多於變數
        unset($data['newstyle'], $data['serial_number'],$data['enrollorg'],$data['enrollname'],$data['base_class_process']);

        // 複選
        $data['rkchk'] = (isset($data['rkchk']) && is_array($data['rkchk']))? ',' . implode(',', $data['rkchk']) . ',' : '';
        $data['subrkchk'] = (isset($data['subrkchk']) && is_array($data['subrkchk']))? ',' . implode(',', $data['subrkchk']) . ',' : '';

        // **上課方式天數
        if($data['style']==4){
	        $data['time1'] = isset($data['time1'])?$data['time1']:'N';
	        $data['time2'] = isset($data['time2'])?$data['time2']:'N';
	        $data['time3'] = isset($data['time3'])?$data['time3']:'N';
	        $data['time4'] = isset($data['time4'])?$data['time4']:'N';
	        $data['time5'] = isset($data['time5'])?$data['time5']:'N';
	        $data['time6'] = isset($data['time6'])?$data['time6']:'N';
	        $data['time7'] = isset($data['time7'])?$data['time7']:'N';
	        $data['holiday'] = isset($data['holiday'])?$data['holiday']:'N';
        }
        // if($data['kind']=='1'){
        //     $data['day'] = $data['period']*5;
        // }elseif($data['kind']=='3'){
        //     $data['day'] = $data['period']/$data['dayhour'];
        // }else{
        //     $data['day'] = $data['period'];
        // }
        // 天數統一
        $data['day'] = $data['trainday'];
        $data['special'] = 'N';
        if(!isset($data['orgchk'])){
            $data['orgchk'] =1;
        }
        if(!isset($data['profchk'])){
            $data['profchk'] ='N';
        }
        if(!isset($data['is_must_read'])){
            $data['is_must_read'] ='0';
        }
        $data['chkclass'] = '';
        $rank = T01tb::where('class','like',$data['yerly'].'%')->max('rank');
        if(empty($rank)) $rank = 0;

        $data['rank'] = $rank+1;
        $data['modifyuser'] = \Auth::user()->userid;
        $data['modifytime'] = date('Y-m-d H:i:s');
        DB::beginTransaction();
        DB::connection()->enableQueryLog();
        try{
            // 更新相同課程
            $this->setSamecourse($data);
            // 新增參訓機關
            if (isset($data['orgchk'])){
                if($data['orgchk'] == '3' && $tempOrgchk != '') {
                $this->setOrgchk('insert', $class, $tempOrgchk);
                }
            }
            // 新增表單
            T01tb::create($data);
            $sql = DB::getQueryLog();
            $nowdata = T01tb::where('class', $data['class'])->get()->toArray();
            unset($nowdata[0]['planmk']);
            createModifyLog('I','t01tb',NULL,$nowdata,end($sql));
            DB::commit();
            return redirect('/admin/classes/'.$data['class'])->with('result', '1')->with('message', '新增成功!');
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message', '新增失敗，請稍後再試!');
        }
    }


    /**
     * 顯示頁
     *
     * @param $classes_id
     */
    public function show($classes_id)
    {
        return $this->edit($classes_id);
    }

    /**
     * 編輯頁
     *
     * @param $classes_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($class)
    {

        $data = T01tb::where('class', $class)->first();
        if ( ! $data) {
            return view('admin/errors/error');
        }

        // 複選轉陣列
        $data->rkchk = array_diff(explode(',', $data->rkchk), array(null, 'null', '', ' '));
        $data->subrkchk = array_diff(explode(',', $data->subrkchk), array(null, 'null', '', ' '));
        $class = $data->class;
        // $data->class = (strlen($class)==7)? substr($class, 0, -1):$class;
        // 取得班別類別
        $classCategory = $this->getClassCategory();
        if(isset($data->commission) && $data->commission!=''){
            $m17 =  M17tb::select('enrollname')->where('enrollorg',$data->commission)->first();
            $data->enrollname = $m17['enrollname'];
        }else{
            $data->enrollname = '';
        }
        $name = M09tb::select('username')->where('userid',$data->modifyuser)->first();
        $data->modifyusername = $name['username'];
        $orgchkPopList = $this->getOrgchk(null, 'edit', $class);
        $sameCourseList = Class_group::select('groupid','class_group')->groupby('groupid')->get()->toarray();
        return view('admin/classes/form', compact('data', 'classCategory', 'orgchkPopList','sameCourseList'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $class)
    {
        // 取得POST資料
        $data = $request->all();
        // dd($data);exit();
        unset($data['_method'], $data['_token'],$data['base_class_process']);
        // 取得參訓機關(限定機關)資料
        $tempOrgchk = $data['tempOrgchk'];
        // 複選
        if($data['rkchk']==''){
            unset($data['rkchk']);
        }else{
            $data['rkchk'] = substr($data['rkchk'],0,-1);
        }

        if($data['subrkchk']==''){
            unset($data['subrkchk']);
        }else{
            $data['subrkchk'] = substr($data['subrkchk'],0,-1);
        }

        if(!isset($data['orgchk'])){
            $data['orgchk'] =1;
        }
        if(!isset($data['profchk'])){
            $data['profchk'] ='N';
        }
        if(!isset($data['is_must_read'])){
            $data['is_must_read'] ='0';
        }
        // **上課方式天數
        if($data['style']==4){
            $data['time1'] = isset($data['time1'])?$data['time1']:'N';
            $data['time2'] = isset($data['time2'])?$data['time2']:'N';
            $data['time3'] = isset($data['time3'])?$data['time3']:'N';
            $data['time4'] = isset($data['time4'])?$data['time4']:'N';
            $data['time5'] = isset($data['time5'])?$data['time5']:'N';
            $data['time6'] = isset($data['time6'])?$data['time6']:'N';
            $data['time7'] = isset($data['time7'])?$data['time7']:'N';
            $data['holiday'] = isset($data['holiday'])?$data['holiday']:'N';
        }
        // 天數統一
        // if($data['kind']=='1'){
        //     $data['day'] = $data['period']*5;
        // }elseif($data['kind']=='3'){
        //     $data['day'] = $data['period']/$data['dayhour'];
        // }else{
        //     $data['day'] = $data['period'];
        // }
        $data['day'] = $data['trainday'];
        // 刪除多於變數
        unset($data['tempOrgchk'],$data['enrollorg'],$data['enrollname']);
        DB::beginTransaction();
        DB::connection()->enableQueryLog();
        try{
            //取消群組
            $olddata = Class_group::where('class',$data['class'])->get();
            Class_group::where('class',$data['class'])->delete();
            $sql = DB::getQueryLog();
            createModifyLog('D','class_group',$olddata,NULL,end($sql));
            // 更新相同課程
            $this->setSamecourse($data);

            if (isset($data['orgchk'])){
                // 更新參訓機關
                if($data['orgchk'] == '3' && $tempOrgchk != '') {
                    $this->setOrgchk('update', $class, $tempOrgchk);
                }
            }
            // 更新
            $olddata = T01tb::where('class', $class)->get()->toArray();
            unset($olddata[0]['planmk']);
            $data['modifyuser'] = \Auth::user()->userid;
            $data['modifytime'] = date('Y-m-d H:i:s');
            T01tb::where('class', $class)->update($data);
            $sql = DB::getQueryLog();
            $nowdata = T01tb::where('class', $class)->get();
            unset($nowdata[0]['planmk']);
            createModifyLog('U','t01tb',$olddata,$nowdata,end($sql));
            DB::commit();
            return back()->with('result', '1')->with('message', '儲存成功!');
        }catch ( Exception $e ){
            DB::rollback();
            return back()->with('result', '0')->with('message', '更新失敗，請稍後再試!');
        }

    }

    //取得跨機關查詢
    function showCrossArea(Request $request){
        $request_data = $request->all();
        $sql = "where 1=1 ";
        if($request_data['organName']!=''){
            $sql .= " and enrollname like'%".$request_data['organName']."%'";
        }
        if($request_data['organCode']!=''){
            $sql .= " and enrollorg like '".$request_data['organCode']."%'";
        }
        if($request_data['crossRadio']=='Y'){
            $sql .= " and crossarea ='Y'";
        }
        $data[] = DB::select("SELECT A.enrollorg, RTRIM(A.enrollname) AS enrollname ,RTRIM(A.crossarea) AS crossarea FROM m17tb A ".$sql);
        return $data;
    }

    //修改跨機關
    function crossOrg(Request $request){
        $request_data = $request->all();
        $sql = "where A.enrollorg = '".$request_data['enrollorg']."'";
        $olddata = DB::select("SELECT A.enrollorg, RTRIM(A.enrollname) AS enrollname ,RTRIM(A.crossarea) AS crossarea FROM m17tb A ".$sql);
        $crossarea = 'N';
        if($olddata[0]->crossarea=='N'){
            $crossarea ='Y';
        }
        DB::update('update m17tb set crossarea = \''.$crossarea.'\' where enrollorg = \''.$request_data['enrollorg'].'\'');
        $sql = DB::getQueryLog();
        $data = DB::select("SELECT A.enrollorg, RTRIM(A.enrollname) AS enrollname ,RTRIM(A.crossarea) AS crossarea FROM m17tb A ".$sql);
        createModifyLog('U','m17tb',$olddata,$data,end($sql));
        return $crossarea;
    }


    /**
     * 刪除處理
     *
     * @param $classes_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($class)
    {
        if ($class) {
            $olddata = T01tb::where('class', $class)->get();
            T01tb::where('class', $class)->delete();
            $sql = DB::getQueryLog();
            createModifyLog('D','t01tb',$olddata,NULL,end($sql));
            // DB::table('t82tb')->where('class', $class)->delete();
            return redirect('admin/classes')->with('result', '1')->with('message', '刪除成功!');

        } else {

            return back()->with('result', '0')->with('message', '刪除失敗!');
        }
    }


    // 共用查詢 取得『班別類別』 資料
    function getClassCategory() {
        $data = DB::select("SELECT serno, indent, CONCAT(name, ' ', category) as name, category, sequence FROM s03tb order by sequence+0");

        return $data;
    }


     // 共用查詢 取得『參訓機關-限定機關』資料
     function getOrgchk(Request $request=null, $type='new', $class='xxxxxx') {
        if($type == 'edit') {
            $year = substr($class, 0, 3);
        }
        else if($type == 'new') {
            $year = date('Y')-1911;
        }

        if(isset($request)) {
            $queryData = $request->all();
            // dd($queryData['class']);
            // $year = $queryData['year'];
            $class = $request->class;
       }

        $data[0] = DB::select("SELECT
                                   A.enrollorg,
                                   RTRIM( A.enrollname ) AS enrollname
                               FROM
                                   m17tb A
                                   INNER JOIN m13tb B ON A.organ = B.organ
                               WHERE
                                   A.grade = '1'
                                   AND B.kind = 'Y'
                                   AND  NOT EXISTS ( SELECT NULL
                                                     FROM t82tb
                                                     WHERE class = '{$class}' AND organ = A.enrollorg )
                               ORDER BY
                                   B.rank");

        $data[1] = DB::select("SELECT A.enrollorg, RTRIM(A.enrollname) AS enrollname
                               FROM m17tb A
                               INNER JOIN t82tb B ON A.enrollorg = B.organ
                               LEFT JOIN m13tb C ON A.organ = C.organ
                               WHERE B.class = '{$class}'
                               GROUP BY A.enrollorg,A.enrollname,C.rank
                               ORDER BY C.rank");

        return $data;
    }

    // 取得『相同課程』資料
    // function getSameCourseList(Request $request=null) {
    //     $queryData = $request->all();
    //     $startClass = $queryData['startClass']!=''?$queryData['startClass']:'000000';
    //     $endClass = $queryData['endClass']!=''?$queryData['endClass']:'ZZZZZZ';
    //     $sameCourse = $queryData['sameCourse']!=''?$queryData['sameCourse']:'000000,';

    //     if(strlen($startClass) < 6) {
    //         $countLen = strlen($startClass);
    //         for($i=0; $i<6-$countLen; $i++) {
    //             $startClass .= '0';
    //         }
    //     }
    //     if(strlen($endClass) < 6) {
    //         $countLen = strlen($endClass);
    //         for($i=0; $i<6-$countLen; $i++) {
    //             $endClass .= 'Z';
    //         }
    //     }

    //     $sameCourse = substr($sameCourse,0,-1);

    //     $data[] = DB::select("SELECT class, name FROM t01tb where left(class,6) between '{$startClass}' and '{$endClass}' AND class NOT IN ({$sameCourse}) ");

    //     $data[] = DB::select("SELECT class, name FROM t01tb where class in ({$sameCourse})");

    //     return $data;
    // }

    // 新增『參訓機關』資料
    function setOrgchk($type, $class, $tempOrgchk) {
        $data = explode(",", substr($tempOrgchk,0,-1));

        if($type == 'update') {
            $olddata = DB::select("select * from t82tb where class='".$class."'");
            DB::delete("DELETE FROM t82tb WHERE class='".$class."'");
            $sql = DB::getQueryLog();
            createModifyLog('D','t82tb',$olddata,NULL,end($sql));
        }

        for($i=0; $i<sizeof($data); $i++) {
            DB::insert("INSERT INTO t82tb (class, organ) VALUES ('".$class."','".$data[$i]."')");
            $data = DB::select("select * from t82tb where class='".$class."' AND organ='".$data[$i]."'"); 
            $sql = DB::getQueryLog();
            createModifyLog('I','t82tb',NULL,$data,end($sql));
        }
    }
    // 設定『相同課程』資料
    private function setSamecourse($data=array()){
        if($data['samecourse']!=''){
            $class_group = Class_group::select('class_group')->where('groupid',$data['samecourse'])->first();
            if(is_null($class_group)) return back()->with('result', '0')->with('message', '新增失敗，無此群組!');
            Class_group::create(array(  'groupid'       =>$data['samecourse'],
                                        'class_group'   =>$class_group['class_group'],
                                        'class'         =>$data['class'],
                                        'name'          =>$data['name'],
                                        'branchcode'    =>$data['branchcode']));
            $sql = DB::getQueryLog();
            $data = Class_group::where('groupid',$data['samecourse'])->where('class',$data['class'])->get();
            createModifyLog('I','class_group',NULL,$data,end($sql));
            
        }
    }
    private function input_csv($handle) {
        $out = array ();
        $n = 0;
        while ($data = fgetcsv($handle, 10000)) {
            $num = count($data);
            for ($i = 0; $i < $num; $i++) {
                $out[$n][$i] = $data[$i];
            }
            $n++;
        }
        return $out;
    }

    //確保輸出內容符合 CSV 格式，定義下列方法來處理
    private function csvstr(array $fields){
        $f = fopen('php://memory', 'r+');
        if (fputcsv($f, $fields) === false) {
            return false;
        }
        rewind($f);
        $csv_line = stream_get_contents($f);
        return rtrim($csv_line);
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
    private function __result( $code,$msg ){
        echo json_encode(array('status' => $code , 'msg' => $msg));
    exit;
    }
}
