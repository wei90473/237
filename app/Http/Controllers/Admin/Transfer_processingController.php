<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Transfer_processingService;
use App\Services\User_groupService;
use App\Models\T08tb;
use App\Models\T09tb;
use App\Models\T01tb;
use App\Models\M01tb;
use App\Models\T04tb;
use DB ;

class Transfer_processingController extends Controller
{
    /**
     * Transfer_processingController constructor.
     * @param Transfer_processingService $transfer_processingService
     */
    public function __construct(Transfer_processingService $transfer_processingService, User_groupService $user_groupService)
    {
        $this->transfer_processingService = $transfer_processingService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('transfer_processing', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
        setProgid('transfer_processing');
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
    }

    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $sponsor = $this->transfer_processingService->getSponsor();
        //年
        $this_yesr = date('Y') - 1911;
        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($queryData['year_list']);
        // echo "\n</pre>\n";
        if(null == $request->get('yerly')){
            $queryData['yerly'] = $this_yesr;
        }else{
            $queryData['yerly'] = $request->get('yerly');
        }
        //班號
        $queryData['class'] = $request->get('class');
        $queryData['name'] = $request->get('name');

        // 分班名稱**
        $queryData['class_branch_name'] = $request->get('class_branch_name');
        // 期別
        $queryData['term'] = $request->get('term');
        // 辦班院區
        $queryData['branch'] = $request->get('branch');
        // 班別類型
        $queryData['process'] = $request->get('process');

        $queryData['sponsor'] = $request->get('sponsor');
        // 訓練性質
        $queryData['traintype'] = $request->get('traintype');
        // 班別性質
        $queryData['type'] = $request->get('type');
        $queryData['sitebranch'] = $request->get('sitebranch');
        $queryData['categoryone'] = $request->get('categoryone');
        $queryData['sdate'] = $request->get('sdate');
        $queryData['edate'] = $request->get('edate');
        $queryData['sdate2'] = $request->get('sdate2');
        $queryData['edate2'] = $request->get('edate2');
        $queryData['sdate3'] = $request->get('sdate3');
        $queryData['edate3'] = $request->get('edate3');
        // 遴聘與否
        $queryData['paid'] = $request->get('paid');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 取得列表資料
        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($queryData);
        // echo "\n</pre>\n";
        // die();
        $queryData['search'] = $request->get('search');

        if($queryData['search'] != 'search' ){
          $queryData2['class'] = 'none';
          $data = $this->transfer_processingService->getTransfer_processingList($queryData2);
        }else{
          $data = $this->transfer_processingService->getTransfer_processingList($queryData);
        }
        // dd($data);
        return view('admin/transfer_processing/list', compact('data', 'queryData', 'sponsor'));
    }

    public function transfer(Request $request)
    {
      $request_data = $request->all();

      // dd($request_data);
      $T04tbKind = $this->transfer_processingService->getT04tbKind($request_data);

      if(!empty($T04tbKind)){
        $kind_msg = '以下班期 【 開支科目 】 空白<br>';
        foreach($T04tbKind as $row){
          $kind_msg .= "代碼:".$row['class']."  期別:".$row['term']."  名稱:".$row['name']."<br>";
        }
        return back()->with('result', '0')->with('html_message', $kind_msg);

      }

      $check_data = $this->transfer_processingService->checkIdno($request_data);

      if(!empty($check_data)){
        $check_msg = '錯誤訊息<br>';
        foreach($check_data as $row){
          $check_msg .= $row['idno'].'--'.$row['cname'].'--'.$row['error_msg']."<br>";
        }
        return back()->with('result', '0')->with('html_message', $check_msg);

      }

      $TransferExists = $this->transfer_processingService->TransferExists($request_data);

      if($TransferExists === true){
        return back()->with('result', '0')->with('html_message', '已做過轉存，請先執行沖銷轉帳');
      }

      $doTransfer_data = $this->transfer_processingService->doTransfer($request_data);

      if($doTransfer_data === false){
        return back()->with('result', '0')->with('html_message', '無符合條件之資料');
      }else{
        return back()->with('result', '1')->with('html_message', '【轉帳】完畢!');
      }

    }

    public function cancelTransfer(Request $request)
    {
    	$request_data = $request->all();

    	$doCancel_data = $this->transfer_processingService->doCancel($request_data);

		if($doCancel_data === false){
			return back()->with('result', '0')->with('html_message', '無符合條件之資料');
		}else{
			return back()->with('result', '1')->with('html_message', '【執行沖銷轉帳】完畢!');
		}

    }

    public function frmFile(Request $request)
    {
    	$request_data = $request->all();

    	$frmFile_text = $this->transfer_processingService->getFile($request_data);

    	if(empty($frmFile_text)){
			return back()->with('result', '0')->with('html_message', '無符合條件之資料');
		}else{
			$myfile = fopen("PSBP-PAY-NEW.txt", "w") or die("Unable to open file!");

			fwrite($myfile, $frmFile_text);
			fclose($myfile);

			return response()->download('PSBP-PAY-NEW.txt');
		}

    }


}
