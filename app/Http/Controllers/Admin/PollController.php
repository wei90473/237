<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\PollService;
use App\Models\T77tb;
use App\Models\T78tb;


class PollController extends Controller
{
    /**
     * PollController constructor.
     * @param PollService $pollService
     */
    public function __construct(PollService $pollService)
    {
        $this->pollService = $pollService;
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
        $data = $this->pollService->getPollList($queryData);

        return view('admin/poll/list', compact('data', 'queryData'));
    }

    /**
     * 新增頁
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin/poll/form');
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
        $data['subject'] = $request->input('subject');
        $data['sdate'] = $request->input('sdate');
        $data['edate'] = $request->input('edate');

        // 日期處理
        $data['sdate'] = str_pad($data['sdate']['year'] ,3,'0',STR_PAD_LEFT).str_pad($data['sdate']['month'] ,2,'0',STR_PAD_LEFT).str_pad($data['sdate']['day'] ,2,'0',STR_PAD_LEFT);
        $data['edate'] = str_pad($data['edate']['year'] ,3,'0',STR_PAD_LEFT).str_pad($data['edate']['month'] ,2,'0',STR_PAD_LEFT).str_pad($data['edate']['day'] ,2,'0',STR_PAD_LEFT);

        //新增
        $result = T77tb::create($data);

        // 更新選項內容
        $this->pollService->updateAnswers($request, $result->serno);

        return redirect('/admin/poll/'.$result->serno)->with('result', '1')->with('message', '新增成功!');
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
        $data = T77tb::find($serno);

        if ( ! $data) {

            return view('admin/errors/error');
        }

        $answersList = T78tb::where('serno', $serno)->get();

        return view('admin/poll/form', compact('data', 'answersList'));
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
        $data['subject'] = $request->input('subject');
        $data['sdate'] = $request->input('sdate');
        $data['edate'] = $request->input('edate');

        // 日期處理
        $data['sdate'] = str_pad($data['sdate']['year'] ,3,'0',STR_PAD_LEFT).str_pad($data['sdate']['month'] ,2,'0',STR_PAD_LEFT).str_pad($data['sdate']['day'] ,2,'0',STR_PAD_LEFT);
        $data['edate'] = str_pad($data['edate']['year'] ,3,'0',STR_PAD_LEFT).str_pad($data['edate']['month'] ,2,'0',STR_PAD_LEFT).str_pad($data['edate']['day'] ,2,'0',STR_PAD_LEFT);

        //更新
        T77tb::find($serno)->update($data);

        // 更新選項內容
        $this->pollService->updateAnswers($request, $serno);

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

            T77tb::find($serno)->delete();

            T78tb::where('serno', $serno)->delete();

            return back()->with('result', '1')->with('message', '刪除成功!');

        } else {

            return back()->with('result', '0')->with('message', '刪除失敗!');
        }
    }
}
