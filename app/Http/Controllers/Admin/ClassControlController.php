<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ClassControlService;
use App\Models\M01tb;


class ClassControlController extends Controller
{
    /**
     * ClassControlController constructor.
     * @param ClassControlService $classControlService
     */
    public function __construct(ClassControlService $classControlService)
    {
        $this->classControlService = $classControlService;
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
        $queryData['type'] = $request->get('type');

        // 取得列表資料
        $data = $this->classControlService->getClassControlList($queryData);

        return view('admin/class_control/list', compact('data', 'queryData'));
    }

    /**
     * 新增頁
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin/class_control/form');
    }




    /**
     * 新增處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();
        // 姓名組成
        $data['cname'] = $data['fname'].$data['lname'];
        unset($data['lname']);

        // 出生日期
        $data['birth'] = ( ! $data['birth'])? NULL : str_pad($data['birth']['year'] ,3,'0',STR_PAD_LEFT).str_pad($data['birth']['month'] ,2,'0',STR_PAD_LEFT).str_pad($data['birth']['day'] ,2,'0',STR_PAD_LEFT);

        //新增
        $result = M01tb::create($data);

        return redirect('/admin/class_control/'.$result->serno)->with('result', '1')->with('message', '新增成功!');
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
        $data = M01tb::find($serno);

        if ( ! $data) {

            return view('admin/errors/error');
        }
        
        return view('admin/class_control/form', compact('data'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $serno)
    {
        $data = $request->all();
        unset($data['_method'], $data['_token']);
        // 姓名組成
        $data['cname'] = $data['fname'].$data['lname'];
        unset($data['lname']);

        // 出生日期
        $data['birth'] = ( ! $data['birth'])? NULL : str_pad($data['birth']['year'] ,3,'0',STR_PAD_LEFT).str_pad($data['birth']['month'] ,2,'0',STR_PAD_LEFT).str_pad($data['birth']['day'] ,2,'0',STR_PAD_LEFT);

        //更新
        M01tb::where('serno', $serno)->update($data);

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

            M01tb::find($serno)->delete();

            return back()->with('result', '1')->with('message', '刪除成功!');

        } else {

            return back()->with('result', '0')->with('message', '刪除失敗!');
        }
    }
}
