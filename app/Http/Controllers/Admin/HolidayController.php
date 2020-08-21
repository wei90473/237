<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\HolidayService;
use App\Services\User_groupService;
use Illuminate\Support\Facades\Hash;
use App\Models\Holiday;
use App\Models\M12tb;


class HolidayController extends Controller
{
    /**
     * HolidayController constructor.
     * @param HolidayService $holidayService
     */
    public function __construct(HolidayService $holidayService, User_groupService $user_groupService)
    {
        $this->holidayService = $holidayService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('holiday', $user_group_auth)){
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
        // 名稱
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
        $data = $this->holidayService->getHolidayList($queryData);

        return view('admin/holiday/list', compact('data', 'queryData'));
    }

    /**
     * 新增頁
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin/holiday/form');
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
        $data['holiday'] = $request->input('holiday');
        $data['date'] = $request->input('date');

        // 日期
        $data['date'] = str_pad($data['date']['year'] ,3,'0',STR_PAD_LEFT).str_pad($data['date']['month'] ,2,'0',STR_PAD_LEFT).str_pad($data['date']['day'] ,2,'0',STR_PAD_LEFT);

        // 檢查日期不能重複
        if (M12tb::where('date', $data['date'])->exists()) {

            return back()->withInput()->with('result', '0')->with('message', '日期已存在，不能重複！');
        }

        // 新增資料
        $result = M12tb::create($data);

        return redirect('/admin/holiday/'.$result->holiday_id)->with('result', '1')->with('message', '新增成功!');
    }

    /**
     * 顯示頁
     *
     * @param $holiday_id
     */
    public function show($date)
    {
        return $this->edit($date);
    }

    /**
     * 編輯頁
     *
     * @param $holiday_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($date)
    {
        $data = M12tb::where('date', $date)->first( );

        if ( ! $data) {

            return view('admin/errors/error');
        }

        return view('admin/holiday/form', compact('data'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $date)
    {
        // 取得POST資料
        $data['holiday'] = $request->input('holiday');

        //更新
        M12tb::where('date', $date)->update($data);

        return back()->with('result', '1')->with('message', '儲存成功!');
    }

    /**
     * 刪除處理
     *
     * @param $holiday_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($date)
    {
        if ($date) {

            M12tb::where('date', $date)->delete();

            return back()->with('result', '1')->with('message', '刪除成功!');

        } else {

            return back()->with('result', '0')->with('message', '刪除失敗!');
        }
    }
}
