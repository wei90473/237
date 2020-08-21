<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ForumService;
use App\Models\T33tb;
use App\Models\T34tb;


class ForumController extends Controller
{
    /**
     * ForumController constructor.
     * @param ForumService $forumService
     */
    public function __construct(ForumService $forumService)
    {
        $this->forumService = $forumService;
    }

    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 種類
        $queryData['type'] = $request->get('type', '1');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 取得列表資料
        $data = $this->forumService->getForumList($queryData);

        return view('admin/forum/list', compact('data', 'queryData'));
    }

    /**
     * 刪除處理
     *
     * @param $forum_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function t33($subjectid)
    {
        if ($subjectid) {

            T33tb::where('subjectid', $subjectid)->delete();

            T34tb::where('subjectid', $subjectid)->delete();

            return back()->with('result', '1')->with('message', '刪除成功!');

        } else {

            return back()->with('result', '0')->with('message', '刪除失敗!');
        }
    }

    /**
     * 刪除處理
     *
     * @param $forum_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function t34($articleid)
    {
        if ($articleid) {

            T34tb::where('articleid', $articleid)->delete();

            return back()->with('result', '1')->with('message', '刪除成功!');

        } else {

            return back()->with('result', '0')->with('message', '刪除失敗!');
        }
    }
}
