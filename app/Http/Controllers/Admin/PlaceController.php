<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\PlaceService;
use App\Services\User_groupService;
use Illuminate\Support\Facades\Hash;
use App\Models\M14tb;


class PlaceController extends Controller
{
    /**
     * PlaceController constructor.
     * @param PlaceService $placeService
     */
    public function __construct(PlaceService $placeService, User_groupService $user_groupService)
    {
        $this->placeService = $placeService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('place', $user_group_auth)){
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
        $queryData['site'] = $request->get('site');
        $queryData['branch'] = $request->get('branch');
        $queryData['name'] = $request->get('name');
        // 場地類型
        $queryData['type'] = $request->get('type');
        // 名稱
        // $queryData['keyword'] = $request->get('keyword');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 取得列表資料
        $data = $this->placeService->getPlaceList($queryData);

        return view('admin/place/list', compact('data', 'queryData'));
    }

    /**
     * 新增頁
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin/place/form');
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

        // 檢查編號不能重複
        if (M14tb::where('site', $data['site'])->exists()) {

            return back()->withInput()->with('result', '0')->with('message', '場地編號不可重複，請重新輸入');
        }

        // 新增資料
        $result = M14tb::create($data);

        return redirect('/admin/place/'.$result->site)->with('result', '1')->with('message', '新增成功!');
    }

    /**
     * 顯示頁
     *
     * @param $site
     */
    public function show($site)
    {
        return $this->edit($site);
    }

    /**
     * 編輯頁
     *
     * @param $site
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($site)
    {
        $data = M14tb::where('site', $site)->first( );

        if ( ! $data) {

            return view('admin/errors/error');
        }

        return view('admin/place/form', compact('data'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $site)
    {
        // 取得POST資料
        $data = $request->all();
        unset($data['_method'], $data['_token']);

        //更新
        M14tb::where('site', $site)->update($data);

        return back()->with('result', '1')->with('message', '儲存成功!');

    }

    /**
     * 刪除處理
     *
     * @param $site
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($site)
    {
        if ($site) {

            M14tb::where('site', $site)->delete();

            return back()->with('result', '1')->with('message', '刪除成功!');

        } else {

            return back()->with('result', '0')->with('message', '刪除失敗!');
        }
    }
}
