<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Tax_processingService;
use App\Services\User_groupService;
use App\Models\T08tb;
use App\Models\T09tb;
use App\Models\T01tb;
use App\Models\M01tb;
use App\Models\T04tb;
use DB ;

class Tax_processingController extends Controller
{
    /**
     * Tax_processingController constructor.
     * @param Tax_processingService $tax_processingService
     */
    public function __construct(Tax_processingService $tax_processingService, User_groupService $user_groupService)
    {
        $this->tax_processingService = $tax_processingService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('tax_processing', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
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
        $queryData['idno'] = $request->get('idno');
        $queryData['name'] = $request->get('name');

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
          $queryData2['idno'] = 'none';
          $data = $this->tax_processingService->getTax_processingList($queryData2);
        }else{
          $data = $this->tax_processingService->getTax_processingList($queryData);
        }

        return view('admin/tax_processing/list', compact('data', 'queryData', 'sponsor'));
    }

    public function taxReturn(Request $request)
    {
      $request_data = $request->all();

      if(!isset($request_data['repeat'])){
          $TaxExists = $this->tax_processingService->TaxExists($request_data);

          if($TaxExists === true){
            return back()->with('result', '0')->with('html_message', $request_data['yerly'].'年度已執行過報稅處理，需要重新執行請勾選重新執行');
          }
      }

      $taxReturn_data = $this->tax_processingService->taxReturn($request_data);

      if($taxReturn_data === false){
        return back()->with('result', '0')->with('html_message', '無符合條件之資料');
      }else{
        return back()->with('result', '1')->with('html_message', '執行報稅處理完畢!');
      }

    }

    public function frmFile(Request $request)
    {
    	$request_data = $request->all();

      $frmFile_name = $this->tax_processingService->getFileName($request_data);

    	$frmFile_text = $this->tax_processingService->getFile($request_data);

    	if(empty($frmFile_text)){
  			return back()->with('result', '0')->with('html_message', '無符合條件之資料');
  		}else{
  			$myfile = fopen($frmFile_name, "w") or die("Unable to open file!");

  			fwrite($myfile, $frmFile_text);
  			fclose($myfile);

  			return response()->download($frmFile_name);
  		}

    }


}
