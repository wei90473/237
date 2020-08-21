<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\TrainService;
use App\Models\T01tb;


class TrainController extends Controller
{
    /**
     * TrainController constructor.
     * @param TrainService $trainService
     */
    public function __construct(TrainService $trainService)
    {
        $this->trainService = $trainService;
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
        $queryData['year'] = $request->get('year');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 取得列表資料
        $data = $this->trainService->getTrainList($queryData);

        return view('admin/train/list', compact('data', 'queryData'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $data['publish'] = $request->input('publish');
        $class = $request->input('class');

        //更新
        T01tb::where('class', $class)->update($data);

        return back()->with('result', '1')->with('message', '儲存成功!');
    }
}
