<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\InstitutionService;
use App\Services\User_groupService;
use Illuminate\Support\Facades\Hash;
use App\Models\M13tb;


class InstitutionController extends Controller
{
    /**
     * InstitutionController constructor.
     * @param InstitutionService $institutionService
     */
    public function __construct(InstitutionService $institutionService, User_groupService $user_groupService)
    {
        $this->institutionService = $institutionService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('institution', $user_group_auth)){
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
        // 年度
        $queryData['organ'] = $request->get('organ');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 取得列表資料
        $data = $this->institutionService->getInstitutionList($queryData);

        return view('admin/institution/list', compact('data', 'queryData'));
    }

    /**
     * 新增頁
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin/institution/form');
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

        // 日期
        // $data['effdate'] = str_pad($data['effdate']['year'] ,3,'0',STR_PAD_LEFT).str_pad($data['effdate']['month'] ,2,'0',STR_PAD_LEFT).str_pad($data['effdate']['day'] ,2,'0',STR_PAD_LEFT);
        // $data['expdate'] = str_pad($data['expdate']['year'] ,3,'0',STR_PAD_LEFT).str_pad($data['expdate']['month'] ,2,'0',STR_PAD_LEFT).str_pad($data['expdate']['day'] ,2,'0',STR_PAD_LEFT);
        // 檢查空白日期,替換為NULL
        // $data['expdate'] = ($data['expdate'] == '0000000')? NULL : $data['expdate'];

        // 檢查機關代碼不能重複
        if (M13tb::where('organ', $data['organ'])->exists()) {

            return back()->withInput()->with('result', '0')->with('message', '機關代碼已存在！');
        }

        // 新增資料
        $result = M13tb::create($data);

        return redirect('/admin/institution/'.$result->organ)->with('result', '1')->with('message', '新增成功!');
    }

    /**
     * 顯示頁
     *
     * @param $organ
     */
    public function show($organ)
    {
        return $this->edit($organ);
    }

    /**
     * 編輯頁
     *
     * @param $organ
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($organ)
    {
        $data = M13tb::where('organ', $organ)->first( );

        if ( ! $data) {

            return view('admin/errors/error');
        }

        return view('admin/institution/form', compact('data'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $organ)
    {
        // 取得POST資料
        $data = $request->all();
        unset($data['_method'], $data['_token']);

        // 日期
        // $data['effdate'] = str_pad($data['effdate']['year'] ,3,'0',STR_PAD_LEFT).str_pad($data['effdate']['month'] ,2,'0',STR_PAD_LEFT).str_pad($data['effdate']['day'] ,2,'0',STR_PAD_LEFT);
        // $data['expdate'] = str_pad($data['expdate']['year'] ,3,'0',STR_PAD_LEFT).str_pad($data['expdate']['month'] ,2,'0',STR_PAD_LEFT).str_pad($data['expdate']['day'] ,2,'0',STR_PAD_LEFT);
        // 檢查空白日期,替換為NULL
        // $data['expdate'] = ($data['expdate'] == '0000000')? NULL : $data['expdate'];

        //更新
        M13tb::where('organ', $organ)->update($data);

        return back()->with('result', '1')->with('message', '儲存成功!');

    }

    /**
     * 刪除處理
     *
     * @param $organ
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($organ)
    {
        if ($organ) {

            M13tb::where('organ', $organ)->delete();

            return back()->with('result', '1')->with('message', '刪除成功!');

        } else {

            return back()->with('result', '0')->with('message', '刪除失敗!');
        }
    }
}
