<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\NewsEnService;
use App\Models\T76tb;


class NewsEnController extends Controller
{
    /**
     * NewsEnController constructor.
     * @param NewsEnService $newsEnService
     */
    public function __construct(NewsEnService $newsEnService)
    {
        $this->newsEnService = $newsEnService;
    }

    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 取得關鍵字
        $queryData['keyword'] = $request->get('keyword');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 取得列表資料
        $data = $this->newsEnService->getNewsEnList($queryData);

        return view('admin/news_en/list', compact('data', 'queryData'));
    }

    /**
     * 新增頁
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin/news_en/form');
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

        $data['sdate'] = str_pad($data['sdate']['year'] ,3,'0',STR_PAD_LEFT).str_pad($data['sdate']['month'] ,2,'0',STR_PAD_LEFT).str_pad($data['sdate']['day'] ,2,'0',STR_PAD_LEFT);
        $data['edate'] = str_pad($data['edate']['year'] ,3,'0',STR_PAD_LEFT).str_pad($data['edate']['month'] ,2,'0',STR_PAD_LEFT).str_pad($data['edate']['day'] ,2,'0',STR_PAD_LEFT);

        if ($data['sdate'] >  $data['edate']) {

            return back()->withInput()->with('result', '0')->with('message', '發佈日期晚於失效日期');
        }

        //新增
        $result = T76tb::create($data);

        return redirect('/admin/news_en/'.$result->serno)->with('result', '1')->with('message', '新增成功!');
    }

    /**
     * 顯示頁
     *
     * @param $serno
     */
    public function show($serno)
    {
        return $this->edit($serno);
    }

    /**
     * 編輯頁
     *
     * @param $serno
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($serno)
    {
        $data = T76tb::find($serno);

        if ( ! $data) {

            return view('admin/errors/error');
        }
        
        return view('admin/news_en/form', compact('data'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $serno)
    {
        // 取得POST資料
        $data = $request->all();

        $data['sdate'] = str_pad($data['sdate']['year'] ,3,'0',STR_PAD_LEFT).str_pad($data['sdate']['month'] ,2,'0',STR_PAD_LEFT).str_pad($data['sdate']['day'] ,2,'0',STR_PAD_LEFT);
        $data['edate'] = str_pad($data['edate']['year'] ,3,'0',STR_PAD_LEFT).str_pad($data['edate']['month'] ,2,'0',STR_PAD_LEFT).str_pad($data['edate']['day'] ,2,'0',STR_PAD_LEFT);

        if ($data['sdate'] >  $data['edate']) {

            return back()->withInput()->with('result', '0')->with('message', '發佈日期晚於失效日期');
        }

        //更新
        T76tb::find($serno)->update($data);

        return back()->with('result', '1')->with('message', '儲存成功!');

    }

    /**
     * 刪除處理
     *
     * @param $serno
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($serno)
    {
        if ($serno) {

            T76tb::find($serno)->delete();

            return back()->with('result', '1')->with('message', '刪除成功!');

        } else {

            return back()->with('result', '0')->with('message', '刪除失敗!');
        }
    }
}
