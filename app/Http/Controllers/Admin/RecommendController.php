<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\RecommendService;
use App\Services\User_groupService;
use App\Models\M17tb;


class RecommendController extends Controller
{
    /**
     * RecommendController constructor.
     * @param RecommendService $recommendService
     */
    public function __construct(RecommendService $recommendService, User_groupService $user_groupService)
    {
        $this->recommendService = $recommendService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('recommend', $user_group_auth)){
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
        // 薦送機關代碼
        $queryData['enrollorg'] = $request->get('enrollorg');
        // 主關機關代碼
        $queryData['organ'] = $request->get('organ');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 取得列表資料
        $data = $this->recommendService->getRecommendList($queryData);

        return view('admin/recommend/list', compact('data', 'queryData'));
    }

    /**
     * 新增頁
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin/recommend/form');
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

        // 檢查上層機關代碼
        if ($data['grade'] > 1) {

            if ( ! M17tb::where('enrollorg', $data['uporgan'])->exists()) {

                return back()->withInput()->with('result', '0')->with('message', '上層機關代碼不存在！');
            }
        }

        //新增
        $result = M17tb::create($data);

        return redirect('/admin/recommend/'.$result->enrollorg)->with('result', '1')->with('message', '新增成功!');
    }

    /**
     * 顯示頁
     *
     * @param $enrollorg
     */
    public function show($enrollorg)
    {
        return $this->edit($enrollorg);
    }

    /**
     * 編輯頁
     *
     * @param $enrollorg
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($enrollorg)
    {
        $data = M17tb::where('enrollorg', $enrollorg)->first();

        if ( ! $data) {

            return view('admin/errors/error');
        }

        return view('admin/recommend/form', compact('data'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $enrollorg)
    {
        // 取得POST資料
        $data = $request->all();
        unset($data['_method'], $data['_token']);

        //更新
        M17tb::where('enrollorg', $enrollorg)->update($data);

        return back()->with('result', '1')->with('message', '儲存成功!');

    }

    /**
     * 刪除處理
     *
     * @param $enrollorg
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($enrollorg)
    {
        if ($enrollorg) {

            M17tb::where('enrollorg', $enrollorg)->delete();

            return back()->with('result', '1')->with('message', '刪除成功!');

        } else {

            return back()->with('result', '0')->with('message', '刪除失敗!');
        }
    }
}
