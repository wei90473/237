<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\PrintService;
// use App\Models\T08tb;
use DB ;

class PrintController extends Controller
{
    /**
     * WaitingController constructor.
     * @param PrintService $siteScheduleService
     */
    public function __construct(PrintService $printService)
    {
        $this->printService = $printService;
    }

    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 班號
		if(null == $request->get('classNo')) {
            $queryData['classNo'] = 'null';
        }
	    else {
            $queryData['classNo'] = $request->get('classNo')=='全部'?'':$request->get('classNo');
        }
        // 課程分類
        $queryData['term'] = $request->get('term');
        // 每頁幾筆
        //$queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 取得列表資料
        $data = $this->printService->getPrintList($queryData);
        return view('admin/print/list', compact('data', 'queryData'));
    }

    /**
     * 進入維護頁面
     * 
     */
    public function maintain()
    {
        return view('admin/print/maintain');
    }

    /**
     * 進入新增
     * 
     */
    public function new()
    {
        return view('admin/print/new');
    }
}