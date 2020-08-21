<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\SystemCodeService;
use App\Services\User_groupService;
use App\Models\S01tb;


class SystemCodeController extends Controller
{
    /**
     * SystemCodeController constructor.
     * @param SystemCodeService $systemCodeService
     */
    public function __construct(SystemCodeService $systemCodeService, User_groupService $user_groupService)
    {
        $this->systemCodeService = $systemCodeService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('system_code', $user_group_auth)){
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
        // 關鍵字
        $queryData['keyword'] = $request->get('keyword');
        // 分類
        $queryData['type'] = $request->get('type');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 取得列表資料
        $data = $this->systemCodeService->getSystemCodeList($queryData);
        // $categoryone = S01tb::select('code','name','category')->where('type','L')->get()->toarray();
        return view('admin/system_code/list', compact('data', 'queryData'));
    }

    /**
     * 新增頁
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
    	$categoryone = S01tb::select('code','name')->where('type','L')->get()->toarray();
        return view('admin/system_code/form', compact('categoryone'));
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
        $data['type'] = $request->input('type');
        $data['code'] = $request->input('code');
        $data['name'] = $request->input('name');
        $data['fee'] = $request->input('fee')? $request->input('fee') : 0;
        $data['category'] = $request->input('category')? $request->input('category') : '';
        // 檢查代碼不能重複
        if (S01tb::where('type', $data['type'])->where('code', $data['code'])->exists()) {

            return back()->withInput()->with('result', '0')->with('message', '代碼已存在，不能重複！');
        }

        //新增
        $result = S01tb::create($data);

        return redirect('/admin/system_code/'.$result->type.'/'.$result->code)->with('result', '1')->with('message', '新增成功!');
    }

    /**
     * 顯示頁
     *
     * @param $type
     */
    public function show($type, $code)
    {
        return $this->edit($type, $code);
    }

    /**
     * 編輯頁
     *
     * @param $type
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($type, $code)
    {
        $data = S01tb::where('type', $type)->where('code', $code)->first();

        if ( ! $data) {

            return view('admin/errors/error');
        }
        $categoryone = S01tb::select('code','name')->where('type','L')->get()->toarray();
        return view('admin/system_code/form', compact('data','categoryone'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $type, $code)
    {
        // 取得POST資料
        $data['name'] = $request->input('name');
        $data['fee'] = $request->input('fee')? $request->input('fee') : 0;
        $data['category'] = $request->input('category')? $request->input('category') : '';
        //更新
        S01tb::where('type', $type)->where('code', $code)->update($data);

        return back()->with('result', '1')->with('message', '儲存成功!');

    }

    /**
     * 刪除處理
     *
     * @param $type
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($type, $code)
    {
        if ($type && $code) {

            S01tb::where('type', $type)->where('code', $code)->delete();

            return back()->with('result', '1')->with('message', '刪除成功!');

        } else {

            return back()->with('result', '0')->with('message', '刪除失敗!');
        }
    }
}
