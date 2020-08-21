<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ProgramService;
use App\Services\User_groupService;
use App\Models\M11tb;


class ProgramController extends Controller
{
    /**
     * ProgramController constructor.
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
            if(in_array('program', $user_group_auth)){
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
        // 程式代號
        $queryData['progid'] = $request->get('progid');
        // 功能名稱
        $queryData['progname'] = $request->get('progname');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 取得列表資料
        $data = $this->programService->getProgramList($queryData);

        return view('admin/program/list', compact('data', 'queryData'));
    }

    /**
     * 新增頁
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin/program/form');
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
        $data['progid'] = $request->input('progid');
        $data['progname'] = $request->input('progname');
        $data['logmk'] = $request->input('logmk');

        // 檢查code不能重複
        if (M11tb::where('progid', $data['progid'])->exists()) {

            return back()->withInput()->with('result', '0')->with('message', '程式代碼已存在，不能重複！');
        }

        // 新增資料
        $result = M11tb::create($data);

        return redirect('/admin/program/'.$result->progid)->with('result', '1')->with('message', '新增成功!');
    }

    /**
     * 顯示頁
     *
     * @param $progid
     */
    public function show($progid)
    {
        return $this->edit($progid);
    }

    /**
     * 編輯頁
     *
     * @param $progid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($progid)
    {
        $data = M11tb::where('progid', $progid)->first( );

        if ( ! $data) {

            return view('admin/errors/error');
        }

        return view('admin/program/form', compact('data'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $progid)
    {
        // 取得POST資料
        $data['progname'] = $request->input('progname');
        $data['logmk'] = $request->input('logmk');

        //更新
        M11tb::where('progid', $progid)->update($data);

        return back()->with('result', '1')->with('message', '儲存成功!');
    }

    /**
     * 刪除處理
     *
     * @param $progid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($progid)
    {
        if ($progid) {

            M11tb::where('progid', $progid)->delete();

            return back()->with('result', '1')->with('message', '刪除成功!');

        } else {

            return back()->with('result', '0')->with('message', '刪除失敗!');
        }
    }
}
