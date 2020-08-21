<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\SiteService;
use App\Models\S02tb;
use App\Models\T01tb;
use App\Models\T04tb;


class SiteController extends Controller
{
    /**
     * SiteController constructor.
     * @param SiteService $siteService
     */
    public function __construct(SiteService $siteService)
    {
        $this->siteService = $siteService;
    }
    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 關鍵字
        $queryData['keyword'] = $request->get('keyword');
        // 年度
        $queryData['year'] = $request->get('year');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 取得列表資料
        $data = $this->siteService->getSiteList($queryData);
        // 取得網頁公告年度
        $year = S02tb::select('nightsyear', 'nighteyear')->first();

        return view('admin/site/list', compact('data', 'queryData', 'year'));
    }

    /**
     * 儲存網頁公告年度
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function year(Request $request)
    {

        $data['nightsyear'] = str_pad($request->input('nightsyear'), 3, '0', STR_PAD_LEFT);
        $data['nighteyear'] = str_pad($request->input('nighteyear'), 3, '0', STR_PAD_LEFT);

        S02tb::query()->update($data);

        return back()->with('result', '1')->with('message', '儲存成功!');
    }

    /**
     * 是否公開
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function publish(Request $request)
    {
        $class = $request->input('class');
        $data['publish'] = $request->input('publish');

        T01tb::where('class', $class)->update($data);

        return back()->with('result', '1')->with('message', '儲存成功!');
    }

    /**
     * 報名開放日期
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function date(Request $request)
    {
        $data['pubsdate'] = $request->input('pubsdate');

        $data['pubedate'] = $request->input('pubedate');

        $class = $request->input('class');

        // 日期處理
        $data['pubsdate'] = str_pad($data['pubsdate']['year'] ,3,'0',STR_PAD_LEFT).str_pad($data['pubsdate']['month'] ,2,'0',STR_PAD_LEFT).str_pad($data['pubsdate']['day'] ,2,'0',STR_PAD_LEFT);
        $data['pubedate'] = str_pad($data['pubedate']['year'] ,3,'0',STR_PAD_LEFT).str_pad($data['pubedate']['month'] ,2,'0',STR_PAD_LEFT).str_pad($data['pubedate']['day'] ,2,'0',STR_PAD_LEFT);

        T04tb::where('class', $class)->update($data);

        return back()->with('result', '1')->with('message', '儲存成功!');
    }
}
