<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\StayListService;
use App\Models\T01tb;
use App\Models\T46tb;
use DB;


class StayListController extends Controller
{
    /**
     * StayListController constructor.
     * @param StayListService $stayListService
     */
    public function __construct(StayListService $stayListService)
    {
        $this->stayListService = $stayListService;
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
        $queryData['class'] = $request->get('class');
        // 取得期別
        $queryData['term'] = $request->get('term');
        // 選項(全部、早餐、住宿)
        $queryData['type'] = $request->get('type');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 取得列表資料
        $data = $this->stayListService->getStayListList($queryData);
        // 取得班別列表
        $classList = $this->stayListService->getClassList();

        return view('admin/stay_list/list', compact('data', 'queryData', 'classList'));
    }

    /**
     * 新增頁
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        // 取得班別列表
        $classList = $this->stayListService->getClassList();

        return view('admin/stay_list/form', compact('classList'));
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
        $data['class'] = $request->input('class');
        $data['term'] = $request->input('term');
        $data['date'] = $request->input('date');
        $data['cname'] = $request->input('cname');
        $data['type'] = $request->input('type');

        // 日期格式
        $data['date'] = str_pad($data['date']['year'] ,3,'0',STR_PAD_LEFT).str_pad($data['date']['month'] ,2,'0',STR_PAD_LEFT).str_pad($data['date']['day'] ,2,'0',STR_PAD_LEFT);

        //新增
        $result = T46tb::create($data);

        return redirect('/admin/stay_list/'.$result->serno)->with('result', '1')->with('message', '新增成功!');
    }

    /**
     * 顯示頁
     *
     * @param $stay_list_id
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
        $data = T46tb::find($serno);

        if ( ! $data) {

            return view('admin/errors/error');
        }

        $classData = T01tb::where('class', $data->class)->first();

        return view('admin/stay_list/form', compact('data', 'classData'));
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
        $data['date'] = $request->input('date');
        $data['cname'] = $request->input('cname');
        $data['type'] = $request->input('type');

        // 日期格式
        $data['date'] = str_pad($data['date']['year'] ,3,'0',STR_PAD_LEFT).str_pad($data['date']['month'] ,2,'0',STR_PAD_LEFT).str_pad($data['date']['day'] ,2,'0',STR_PAD_LEFT);

        //更新
        T46tb::find($serno)->update($data);

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

            T46tb::find($serno)->delete();

            return back()->with('result', '1')->with('message', '刪除成功!');

        } else {

            return back()->with('result', '0')->with('message', '刪除失敗!');
        }
    }

    /**
     * 取得期別
     *
     * @param $class
     * @return string
     */
    public function getTerm(Request $request)
    {
        $class = $request->input('classes');

        $selected = $request->input('selected');

        if (is_numeric( mb_substr($class, 0, 1))) {

            $data = DB::select('SELECT DISTINCT term FROM t04tb WHERE class = \''.$class.'\' ORDER BY `term`');
        } else {

            $data = DB::select('SELECT DISTINCT term FROM t38tb WHERE meet = \''.$class.'\' ORDER BY `term`');
        }

        $result = '';

        foreach ($data as $va) {
            $result .= '<option value="'.$va->term.'"';
            $result .= ($selected == $va->term)? ' selected>' : '>';
            $result .= $va->term.'</option>';
        }

        return $result;
    }
}
