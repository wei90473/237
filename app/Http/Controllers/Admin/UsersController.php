<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\UsersService;
use App\Models\M22tb;


class UsersController extends Controller
{
    /**
     * UsersController constructor.
     * @param UsersService $usersService
     */
    public function __construct(UsersService $usersService)
    {
        $this->usersService = $usersService;
    }

    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 身分證字號
        $queryData['userid'] = $request->get('userid');
        // 姓名
        $queryData['name'] = $request->get('name');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 取得列表資料
        $data = $this->usersService->getUsersList($queryData);

        return view('admin/users/list', compact('data', 'queryData'));
    }

    /**
     * 新增頁
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin/users/form');
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

        // checkbox
        $data['usertype1'] = isset($data['usertype1'])? $data['usertype1'] : 'N';
        $data['usertype2'] = isset($data['usertype2'])? $data['usertype2'] : 'N';
        $data['usertype3'] = isset($data['usertype3'])? $data['usertype3'] : 'N';
        $data['chief'] = isset($data['chief'])? $data['chief'] : 'N';
        $data['personnel'] = isset($data['personnel'])? $data['personnel'] : 'N';
        $data['aborigine'] = isset($data['aborigine'])? $data['aborigine'] : 'N';
        $data['vegan'] = isset($data['vegan'])? $data['vegan'] : 'N';
        $data['handicap'] = isset($data['handicap'])? $data['handicap'] : 'N';

        // 日期
        $data['birth'] = str_pad($data['birth']['year'] ,3,'0',STR_PAD_LEFT).str_pad($data['birth']['month'] ,2,'0',STR_PAD_LEFT).str_pad($data['birth']['day'] ,2,'0',STR_PAD_LEFT);

        //新增
        $result = M22tb::create($data);

        return redirect('/admin/users/'.$result->users_id)->with('result', '1')->with('message', '新增成功!');
    }

    /**
     * 顯示頁
     *
     * @param $users_id
     */
    public function show($users_id)
    {
        return $this->edit($users_id);
    }

    /**
     * 編輯頁
     *
     * @param $users_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($userid)
    {
        $data = M22tb::where('userid', $userid)->first();

        if ( ! $data) {

            return view('admin/errors/error');
        }
        
        return view('admin/users/form', compact('data'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $userid)
    {
        // 取得POST資料
        $data = $request->all();
        unset($data['_method'], $data['_token']);
        // checkbox
        $data['usertype1'] = isset($data['usertype1'])? $data['usertype1'] : 'N';
        $data['usertype2'] = isset($data['usertype2'])? $data['usertype2'] : 'N';
        $data['usertype3'] = isset($data['usertype3'])? $data['usertype3'] : 'N';
        $data['chief'] = isset($data['chief'])? $data['chief'] : 'N';
        $data['personnel'] = isset($data['personnel'])? $data['personnel'] : 'N';
        $data['aborigine'] = isset($data['aborigine'])? $data['aborigine'] : 'N';
        $data['vegan'] = isset($data['vegan'])? $data['vegan'] : 'N';
        $data['handicap'] = isset($data['handicap'])? $data['handicap'] : 'N';

        // 日期
        $data['birth'] = str_pad($data['birth']['year'] ,3,'0',STR_PAD_LEFT).str_pad($data['birth']['month'] ,2,'0',STR_PAD_LEFT).str_pad($data['birth']['day'] ,2,'0',STR_PAD_LEFT);

        //更新
        M22tb::where('userid', $userid)->update($data);

        return back()->with('result', '1')->with('message', '儲存成功!');

    }

    /**
     * 刪除處理
     *
     * @param $users_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($userid)
    {
        if ($userid) {

            M22tb::where('userid', $userid)->delete();

            return back()->with('result', '1')->with('message', '刪除成功!');

        } else {

            return back()->with('result', '0')->with('message', '刪除失敗!');
        }
    }
}
