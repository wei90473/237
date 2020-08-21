<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\SiteCheckService;
use App\Models\T38tb;
use DB;


class SiteCheckController extends Controller
{
    /**
     * SiteCheckController constructor.
     * @param SiteCheckService $sitecheckService
     */
    public function __construct(SiteCheckService $sitecheckService)
    {
        $this->sitecheckService = $sitecheckService;
    }

    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 取得班別
        $queryData['start_date'] = $request->get('start_date');
        // 取得期別
        $queryData['end_date'] = $request->get('end_date');
        // 是否審核
        $queryData['status'] = $request->get('status');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 取得列表資料
        $data = $this->sitecheckService->getSiteCheckList($queryData);

        return view('admin/site_check/list', compact('data', 'queryData'));
    }

    /**
     * 顯示頁
     *
     * @param $site_check_id
     */
    public function show($site_check_id)
    {
        return $this->edit($site_check_id);
    }

    /**
     * 編輯頁
     *
     * @param $site_check_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($site_check_id)
    {
        $data = T38tb::find($site_check_id);

        if ( ! $data) {

            return view('admin/errors/error');
        }

        return view('admin/site_check/form', compact('data'));
    }

    /**
     * 通過
     *
     * @param $id
     */
    public function pass($id)
    {
        T38tb::where('id', $id)->update(['prove' => 'Y']);

        return back()->with('result', '1')->with('message', '修改成功!');
    }

    /**
     * 退回
     *
     * @param $id
     */
    public function returns($id)
    {
        T38tb::where('id', $id)->update(['prove' => 'R']);

        return back()->with('result', '1')->with('message', '修改成功!');
    }

    /**
     * 取消
     *
     * @param $id
     */
    public function cancel($id)
    {
        T38tb::where('id', $id)->update(['prove' => 'W']);

        return back()->with('result', '1')->with('message', '修改成功!');
    }
}
