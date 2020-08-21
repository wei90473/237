<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ProgramService;
use App\Services\User_groupService;
use App\Models\M11tb;
use DB ;

class ProgramSearchController extends Controller
{
    /**
     * ProgramSearchController constructor.
     * @param ProgramService $programService
     */
    public function __construct(ProgramService $programService, User_groupService $user_groupService)
    {
        $this->programService = $programService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('program_search', $user_group_auth)){
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
        // 使用者帳號
        $queryData['userid'] = $request->get('userid');
        // 程式代號
        $queryData['progid'] = $request->get('progid');
        // 異動類別 I:新增 U:修改 D:刪除 B:批次作業 R:查詢
        $queryData['type'] = $request->get('type');
        // 異動日期
        $queryData['logsdate'] = $request->get('logsdate');
        $queryData['logedate'] = $request->get('logedate');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 取得列表資料
        if(empty($request->all())) {
            return view('admin/program_search/list', compact('queryData'));
        }
        // 取得列表資料
        $data = $this->programService->getSearchList($queryData);

        return view('admin/program_search/list', compact('data', 'queryData'));
    }

}
