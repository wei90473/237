<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\DemandSurveyCommissionedService;
use App\Services\User_groupService;
use App\Models\DemandSurveyCommissioned;
use App\Models\DemandSurveyCommissionedPre;
use App\Models\DemandTransactDates;
use App\Models\T01tb;
use DB;

class DemandSurveyCommissionedController extends Controller
{
    /**
     * DemandSurveyCommissionedController constructor.
     * @param DemandSurveyCommissionedService $demandSurveyCommissionedService
     */
    public function __construct(DemandSurveyCommissionedService $demandSurveyCommissionedService, User_groupService $user_groupService)
    {
        $this->demandSurveyCommissionedService = $demandSurveyCommissionedService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('demand_survey_commissioned', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
            // dd($user_data);
            // dd(\session());
        });
    }

    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 取得查詢年度
        $queryData['yerly'] = $request->get('yerly');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 取得列表資料
        $data = $this->demandSurveyCommissionedService->getDemandSurveyList($queryData);
        
        $queryData['choices'] = $this->_get_year_list();


        return view('admin/demand_survey_commissioned/list', compact('data', 'queryData'));
    }

    /**
     * 新增頁
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin/demand_survey_commissioned/form');
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

        // 取得專碼，同一年專碼不得重覆
        $row_data = DemandSurveyCommissioned::where('item_id', $data['item_id'])->count();

        if($row_data>0){
            return redirect('/admin/demand_survey_commissioned/create')->with('result', '0')->with('message', '新增失敗，該專碼已被使用!');
        }else{
            $result = DemandSurveyCommissioned::create(array(
                'yerly' => $data['yerly'],
                'item_id' => $data['item_id'],
                'sdate' => $data['sdate'],
                'edate' => $data['edate'],
                'remark' => $row_data,
            ));
            return redirect('/admin/demand_survey_commissioned/'.$result->id)->with('result', '1')->with('message', '新增成功!');
        }

    }

    /**
     * 顯示頁
     *
     * @param $id
     */
    public function show($id)
    {
        return $this->edit($id);
    }

    /**
     * 編輯頁
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        $data = DemandSurveyCommissioned::find($id);

        if ( ! $data) {

            return view('admin/errors/error');
        }


        return view('admin/demand_survey_commissioned/form', compact('data'));
    }



    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // 取得POST資料
        $data = $request->all();

       // $data['sdate'] = str_pad($data['sdate']['yerly'] ,3,'0',STR_PAD_LEFT).str_pad($data['sdate']['month'] ,2,'0',STR_PAD_LEFT).str_pad($data['sdate']['day'] ,2,'0',STR_PAD_LEFT);
       // $data['edate'] = str_pad($data['edate']['year'] ,3,'0',STR_PAD_LEFT).str_pad($data['edate']['month'] ,2,'0',STR_PAD_LEFT).str_pad($data['edate']['day'] ,2,'0',STR_PAD_LEFT);

        //更新
        DemandSurveyCommissioned::find($id)->update($data);

        return back()->with('result', '1')->with('message', '儲存成功!');
    }






    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function view(Request $request,$id)
    {

        //取得專碼
        $data = DemandSurveyCommissioned::find($id);
    
        if ( ! $data) {

            return view('admin/errors/error');
        }

        $queryData['id']   = $id;

        // 取得查詢年度
        $queryData['yerly']   = $data['yerly'];
        // 取得專班
        $queryData['item_id'] = $data['item_id'];
        // 取得專班
        $queryData['audit_status'] =  $request->get('audit_status');

        // 排序欄位
        $queryData['_sort_field']   = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode']    = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;

        // 取得列表資料
        $data = $this->demandSurveyCommissionedService->getDemandSurveyPreList($queryData);
        $dataDemandTransact = array();
        foreach ($data as $demandSurvey){
            $data2 = DemandTransactDates::where('id', '=', $demandSurvey->id)->get();
            foreach ($data2 as $demandSurveyData){        
                array_push($dataDemandTransact,$demandSurveyData);            
            }    
        }
        return view('admin/demand_survey_commissioned/commissioned_list',  compact('data','queryData','dataDemandTransact'));
    }


    /**
     *
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function import(Request $request,$id)
    {
        $req_data = $request->all();
        $data = DemandSurveyCommissioned::find($id);
        if ( ! $data) {
            return view('admin/errors/error');
        }
        $queryData['item_id']      = $data['item_id'];
        $queryData['yerly']        = $data['yerly'];
   
        // 取得列表資料
        $data2 = $this->demandSurveyCommissionedService->getDemandSurveyPreList($queryData);
        $maxClassNumber = 0;
        foreach ($data2 as $key => $value){
            
            $dataA = T01tb::where('name', '=', $value->class_name)->where('class','like', $queryData['yerly'] .'%')->count('class');;
     
            if($dataA>0){
                continue;
            }
            if($maxClassNumber == 0){
                $maxClassNumber = $this->getMaxClassNumber($value->yerly);
            }else{
                $maxClassNumber++;
            }
            $create_data = array();
            $create_data['class']          = $value->yerly.  $maxClassNumber .'B';
            $create_data['yerly']          = $queryData['yerly'];       //辦理年度
            $create_data['name']           = $value->class_name;  //班別名稱
            $create_data['object']         = $value->object;      //參加對象
            $create_data['target']         = $value->target;      //研習目標
            $create_data['periods']        = $value->period;       //辦理期數
            $create_data['quota']          = $value->periods_people;      //每期人數
            $create_data['trainday']       = $value->training_days;      //訓練天數
            $create_data['process']        = 2; //委訓班
            $create_data['classified']     = 1; //學習性質
            $create_data['branch']         = 2;// 辦班院區-預設南投
            $create_data['branchcode'] = 'B';
            $create_data['type'] = '13';
            $create_data['rank'] = '';
            $create_data['chkclass'] = '';
          
            //新增
        
            // $result = T01tb::create($create_data);

        }


        // return view('admin/demand_survey_commissioned/import_form', compact('data'));
    }

    /**
     * 取得班別編號流水號
     *
     * @return int|string
     */
    public function getMaxClassNumber($yerly)
    {
       $data = T01tb::where('type', '!=', '13')->where('class','like', $yerly.'%')->max('class');
       return isset($data)? mb_substr($data, 3, 3) + 1 : 1;
    }
    /**
     *
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function import_save(Request $request,$id)
    {

        // 取得POST資料
        $req_data = $request->all();
        if ($request->hasFile('import_file')) {
            $file = $request->file('import_file');  //獲取UploadFile例項

            if ( $file->isValid()) { //判斷檔案是否有效
                $filename = $file->getClientOriginalName(); //檔案原名稱
                $extension = $file->getClientOriginalExtension(); //副檔名
                $filename = time() . "." . $extension;    //重新命名
                $file->move(storage_path('upload'), $filename); //移動至指定目錄
                $filename = storage_path('upload').'/'. $filename;
                $csv_header = '';
                foreach (file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                    $csv = str_getcsv($line);
                    $data = [];
                    if (!$csv_header)
                    {
                        $csv_header = $csv;
                    }
                    else{
                        $data[] = array_combine($csv_header, $csv);
                        $result = DemandSurveyCommissionedPre::create($data[0]);
                    }

                }
            }
        }else{
            die('沒有接收到檔案');
        }

        return back()->with('result', '1')->with('message', '儲存成功!');

    }

    
    
    /**
     * 批次匯入
     *
     * @return file
     */
    public function import_class_data(Request $request,$id)
    {
        $req_data = $request->all();
        $data = DemandSurveyCommissioned::find($id);
        if ( ! $data) {
            return view('admin/errors/error');
        }
        $queryData['item_id']      = $data['item_id'];
        $queryData['yerly']        = $data['yerly'];
        $maxClassNumber = 0;
        foreach ( $req_data['selected'] as $key => $value){
            $data2 = DemandSurveyCommissionedPre::find($value);

            if(isset($data2->enable)){
                if($data2->enable == '已開班'){
                    continue;
                }
            }


            $dataA = T01tb::where('class','like', $queryData['yerly'] .'%')->count('class');
         
            if($maxClassNumber == 0){
                $maxClassNumber = $this->getMaxClassNumber($queryData['yerly']);
            }else{
                $maxClassNumber++;
            }


            $create_data = array();
            $create_data['class']          = $queryData['yerly'].  $maxClassNumber .'B';
            $create_data['yerly']          = $queryData['yerly'];       //辦理年度
            $create_data['name']           = $data2->class_name;  //班別名稱
            $create_data['object']         = $data2->object;      //參加對象
            $create_data['target']         = $data2->target;      //研習目標
            $create_data['periods']        = $data2->period;       //辦理期數
            $create_data['quota']          = $data2->periods_people;      //每期人數
            $create_data['trainday']       = $data2->training_days;      //訓練天數
            $create_data['process']        = 2; //委訓班
            $create_data['classified']     = 1; //學習性質
            $create_data['branch']         = 2;// 辦班院區-預設南投
            $create_data['branchcode'] = 'B';
            $create_data['type'] = '13';
            $create_data['rank'] = '';
            $create_data['chkclass'] = '';
        

            $result = T01tb::create($create_data);
            DemandSurveyCommissionedPre::where('id', $value)->update(array('enable' =>'已開班'));
        }
      
     

    //     $queryData['id']       = $id;
    //     // 取得查詢年度
    //     $queryData['yerly']   = $data['yerly'];
    //  dd($data2);
        // DemandSurveyCommissionedPre::whereIn('id', $req_data['selected'])->update(array('audit_status' =>'審核通過'));
        return json_encode(array('success' =>   '儲存成功!'));
    }
    /*


    /**
     * 審核通過
     *
     * @return file
     */
    public function audit_accept(Request $request)
    {
        // 取得POST資料
        $req_data = $request->all();
        DemandSurveyCommissionedPre::whereIn('id', $req_data['selected'])->update(array('audit_status' =>'審核通過'));
        return json_encode(array('success' => '儲存成功!'));
    }
    /**
     * 審核不通過
     *
     * @return file
     */
    public function audit_reject(Request $request)
    {
        // 取得POST資料
        $req_data = $request->all();

        DemandSurveyCommissionedPre::whereIn('id', $req_data['selected'])->update(array('audit_status' =>'審核不通過'));
        return json_encode(array('success' => '儲存成功!'));
    }

    /**
     * 審核中
     *
     * @return file
     */
    public function audit_ing(Request $request)
    {
        // 取得POST資料
        $req_data = $request->all();
        DemandSurveyCommissionedPre::whereIn('id', $req_data['selected'])->update(array('audit_status' =>'審核中'));
        return json_encode(array('success' => '儲存成功!'));
    }

    /**
     * 輸出匯總資料表word檔案
     *
     * @return file
     */
    public function export_doc(Request $request,$id)
    {
               
        //取得專碼
        $data = DemandSurveyCommissioned::find($id);  
        $queryData['id']       = $id;
        // 取得查詢年度
        $queryData['yerly']   = $data['yerly'];
        // 取得列表資料
        $reportlist = $this->demandSurveyCommissionedService->getDemandSurveyAuditList($queryData);
  
        $data = json_decode(json_encode($reportlist), true);

       
        // 讀檔案
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'DD').'.docx');


        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $templateProcessor->setValue('year',$queryData['yerly']);
        $templateProcessor->cloneRow('entrusting_unit', sizeof($data));
        $i = 0 ;

        foreach ($data as $key => $value){  
            $date_str = '';
        
            
            $data2 = DemandTransactDates::where('id', '=', $value['id'])->get();
            foreach ($data2 as $demandSurveyData){        
                $date_str .= '第'.$demandSurveyData->demand_id.'期'.$demandSurveyData->sdate.'~'.$demandSurveyData->edate;           
            }    
                
            $templateProcessor->setValue('entrusting_unit#'.strval($i+1),$value['entrusting_unit']);
            $templateProcessor->setValue('entrusting_orga#'.strval($i+1),$value['entrusting_orga']);
            $templateProcessor->setValue('class_name#'.strval($i+1),$value['class_name']);
            $templateProcessor->setValue('object#'.strval($i+1),$value['object']);
            $templateProcessor->setValue('target#'.strval($i+1),$value['target']);
            $templateProcessor->setValue('periods#'.strval($i+1),$value['periods']);
            $templateProcessor->setValue('periods_people#'.strval($i+1),$value['periods_people']);
            $templateProcessor->setValue('training_days#'.strval($i+1),$value['training_days']);
            $templateProcessor->setValue('date#'.strval($i+1),$date_str);
            $i++;
        }
        $outputfile = $queryData['yerly'].'年度接受委託辦理訓練需求彙總表';
        $RptBasic = new \App\Rptlib\RptBasic();
        $RptBasic->exportfile($templateProcessor,"1",'1',$outputfile);

        // return response()->download(storage_path($queryData['yerly'].'年度接受委託辦理訓練需求彙總表.docx'));
    }


    /**
     * 輸出匯總資料表word檔案
     *
     * @return file
     */
    public function export_odf(Request $request,$id)
    {


            //取得專碼
            $data = DemandSurveyCommissioned::find($id);  
            $queryData['id']       = $id;
            // 取得查詢年度
            $queryData['yerly']   = $data['yerly'];
            // 取得列表資料
            $reportlist = $this->demandSurveyCommissionedService->getDemandSurveyAuditList($queryData);
      
            $data = json_decode(json_encode($reportlist), true);
    
           
            // 讀檔案
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../example/'.iconv('UTF-8', 'BIG5', 'DD').'.docx');
    
    
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $templateProcessor->setValue('year',$queryData['yerly']);
            $templateProcessor->cloneRow('entrusting_unit', sizeof($data));
            $i = 0 ;
            foreach ($data as $key => $value){     
                $date_str = '';
                $data2 = DemandTransactDates::where('id', '=', $value['id'])->get();
                foreach ($data2 as $demandSurveyData){        
                    $date_str .= '第'.$demandSurveyData->demand_id.'期'.$demandSurveyData->sdate.'~'.$demandSurveyData->edate;           
                }     
                $templateProcessor->setValue('entrusting_unit#'.strval($i+1),$value['entrusting_unit']);
                $templateProcessor->setValue('entrusting_orga#'.strval($i+1),$value['entrusting_orga']);
                $templateProcessor->setValue('class_name#'.strval($i+1),$value['class_name']);
                $templateProcessor->setValue('object#'.strval($i+1),$value['object']);
                $templateProcessor->setValue('target#'.strval($i+1),$value['target']);
                $templateProcessor->setValue('periods#'.strval($i+1),$value['periods']);
                $templateProcessor->setValue('periods_people#'.strval($i+1),$value['periods_people']);
                $templateProcessor->setValue('training_days#'.strval($i+1),$value['training_days']);
                $templateProcessor->setValue('date#'.strval($i+1),$date_str);
                $i++;
            }
            $outputfile = $queryData['yerly'].'年度接受委託辦理訓練需求彙總表';
            $RptBasic = new \App\Rptlib\RptBasic();
            $RptBasic->exportfile($templateProcessor,"1",'2',$outputfile);
    }

    /**
     * 審核修正編輯頁
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function audit_edit($id)
    {
        $data = DemandSurveyCommissionedPre::find($id);

        if ( ! $data) {
            return view('admin/errors/error');
        }
        $dataDemandTransact = array();
        $data2 = DemandTransactDates::where('id', '=', $id)->get();
        $dataDemandTransactCount = 0;
        foreach ($data2 as $demandSurveyData){    
            $dataDemandTransactCount++;    
            array_push($dataDemandTransact,$demandSurveyData);            
        }    
     
     
        return view('admin/demand_survey_commissioned/form_site', compact('data','dataDemandTransact','dataDemandTransactCount'));
    }



    /**
     * 委訓班編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function audit_edit_update(Request $request, $id)
    {
        // 取得POST資料
        $data = $request->all();
        $transactDates = $request->only('newTransactDates', 'transactDates');
        $transactDates['newTransactDates'] = isset($transactDates['newTransactDates']) ? $transactDates['newTransactDates'] : [];
        $transactDates['transactDates'] = isset($transactDates['transactDates']) ? $transactDates['transactDates'] : [];

        if (count($transactDates['newTransactDates']) + count($transactDates['transactDates']) > 20){
            return back()->with('error', '建議辦理日期最多20組'); 
        }

        // 過濾不完整的資料
        $transactDates['newTransactDates'] = array_filter(array_map(function($transactDate){
            if (empty($transactDate['sdate']) || empty($transactDate['edate'])){
                return null;
            }
            return $transactDate;
        }, $transactDates['newTransactDates']));

        $transactDates['transactDates'] = array_filter(array_map(function($transactDate){
            if (empty($transactDate['sdate']) || empty($transactDate['edate'])){
                return null;
            } 
            return $transactDate;
        }, $transactDates['transactDates']));

        if (empty($transactDates['newTransactDates']) && empty($transactDates['transactDates'])){
            return back()->with('error', '至少輸入一組建議辦理日期');
        }
        $demandAllArray = array();
  
        foreach($transactDates['transactDates'] as $key => $value){
            $demandSurveyArray = array();
            $demandSurveyArray['id'] = $id;
            $demandSurveyArray['demand_id'] = $key;
            $demandSurveyArray['sdate']     = $value['sdate'];
            $demandSurveyArray['edate']     =  $value['edate'];
            array_push($demandAllArray,$demandSurveyArray);
        
        }
        foreach($transactDates['newTransactDates'] as $key => $value){
            $demandSurveyArray = array();
            $demandSurveyArray['id'] = $id;
            $demandSurveyArray['demand_id'] = $key;
            $demandSurveyArray['sdate']     = $value['sdate'];
            $demandSurveyArray['edate']     =  $value['edate'];
            array_push($demandAllArray,$demandSurveyArray);
        }

        DB::beginTransaction();
        try{
            DemandTransactDates::where('id',$id )->delete();
            DemandTransactDates::insert($demandAllArray);;
            //更新
            DemandSurveyCommissionedPre::find($id)->update($data);
            DB::commit();       
            return back()->with('result', '1')->with('message', '儲存成功!');
            $update = true;
        } catch (\Exception $e) {
            DB::rollback();            
            return back()->with('result', '0')->with('message', '新增失敗!');
            var_dump($e->getMessage());
            die;            
            $update = false;
        } 


    }



    /**
     * 刪除處理
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {

        if ($id) {

            DemandSurveyCommissioned::find($id)->delete();

            // 刪除相關的需求分配
            // DemandDistribution::where('id', $id)->delete();

            return back()->with('result', '1')->with('message', '刪除成功!');

        } else {

            return back()->with('result', '0')->with('message', '刪除失敗!');
        }
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
