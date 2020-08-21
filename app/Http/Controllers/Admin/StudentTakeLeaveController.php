<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Services\StudentTakeLeaveService;


class StudentTakeLeaveController extends Controller
{

    public function __construct(StudentTakeLeaveService $studentTakeLeaveService)
    {
        $this->studentTakeLeaveService = $studentTakeLeaveService;
    }

    /**
     * 班級列表顯示頁
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function class_list()
    {
        $queryData = $request->only([
            'yerly',                // 年度
            'class',                // 班號
            'class_name',           // 班級名稱
            'sub_class_name',       // 分班名稱
            'term',                 // 期別
            'class_location',       // 上課地點
            'branch',               // 辦班院區
            'process',              // 班別類型
            'entrust_train_unit',   // 委訓單位
            'worker',               // 班務人員
            'train_start_date',     // 開訓日期範圍(起)
            'train_end_date',       // 開訓日期範圍(訖)
            'graduate_start_date',  // 結訓日期範圍(起)
            'graduate_end_date',    // 結訓日期範圍(訖)
            '_paginate_qty',        // 分頁資料數量
        ]);
        
        $data = [];
        if ($request->all()){
            $data = $this->studentTakeLeaveService->getOpenClassList($queryData); // 取得開班資料
        }
         
        return view('admin/student_take_leave/class_list', compact('data', 'queryData'));
    }

    public function index($class, $term)
    {
        return view('admin/student_take_leave/index');
    }

    public function create($class, $term)
    {
        return view('admin/student_take_leave/form');
    }

    public function store(Request $request, $class, $term)
    {

    }

    public function edit($class, $term, $idno, $serno)
    {
        return view('admin/student_take_leave/form');
    }

    public function update(Request $request, $class, $term, $idno, $serno)
    {

    }

}
