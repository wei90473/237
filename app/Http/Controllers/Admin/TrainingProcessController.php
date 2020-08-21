<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\TrainingProcessService;
use App\Models\T01tb;
use App\Models\T53tb;
use App\Models\T58tb;
use App\Models\T59tb;
use App\Models\T60tb;
use DB;


class TrainingProcessController extends Controller
{
    /**
     * TrainingProcessController constructor.
     * @param TrainingProcessService $trainingProcessService
     */
    public function __construct(TrainingProcessService $trainingProcessService)
    {
        $this->trainingProcessService = $trainingProcessService;
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
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 取得列表資料
        $data = $this->trainingProcessService->getTrainingProcessList($queryData);
        // 取得課程列表
        $classList = $this->trainingProcessService->getClassList();

        return view('admin/training_process/list', compact('data', 'queryData', 'classList'));
    }

    /**
     * 新增頁
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        // 取得課程列表
        $classList = $this->trainingProcessService->getClassList();

        $list = array();

        return view('admin/training_process/form', compact('classList', 'list'));
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
        $data['comment'] = $request->input('comment');
        $data['addcourse'] = $request->input('addcourse');
        $data['delcourse'] = $request->input('delcourse');
        $data['wholeval'] = $request->input('wholeval');
        $data['willing'] = $request->input('willing');
        $data['othercom'] = $request->input('othercom');
        // 取得serno
        $data['serno'] = T59tb::where('class', $data['class'])->where('term', $data['term'])->max('serno') + 1;
        $data['serno'] = str_pad($data['serno'] ,3,'0',STR_PAD_LEFT);

        // 檢查
        if ( ! T53tb::where('class', $data['class'])->where('term', $data['term'])->exists()) {
            return back()->with('result', '0')->with('message', '未設定問卷題目!');
        }

        if ( ! T58tb::where('class', $data['class'])->where('term', $data['term'])->exists()) {
            return back()->with('result', '0')->with('message', '未設定問卷題目!');
        }

        $result = T59tb::create($data);

        $ans = $request->input('ans');

        if (is_array($ans)) {
            foreach ($ans as $course => $va) {
                T60tb::create([
                    'class' => $data['class'],
                    'term' => $data['term'],
                    'serno' => $data['serno'],
                    'course' => $course,
                    'ans' => $va,
                ]);
            }
        }

        return redirect('/admin/training_process/'.$result->id)->with('result', '1')->with('message', '新增成功!');
    }

    /**
     * 顯示頁
     *
     * @param $training_process_id
     */
    public function show($training_process_id)
    {
        return $this->edit($training_process_id);
    }

    /**
     * 編輯頁
     *
     * @param $training_process_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($training_process_id)
    {
        $data = T59tb::find($training_process_id);

        if ( ! $data) {

            return view('admin/errors/error');
        }

        $classData = T01tb::select('name', 'class')->where('class', $data->class)->first();

        $list = $this->trainingProcessService->getCourse($data->class, $data->term, $data->serno);
        
        return view('admin/training_process/form', compact('data', 'classData', 'list'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $training_process_id)
    {
        // 取得POST資料
        $data['comment'] = $request->input('comment');
        $data['addcourse'] = $request->input('addcourse');
        $data['delcourse'] = $request->input('delcourse');
        $data['wholeval'] = $request->input('wholeval');
        $data['willing'] = $request->input('willing');
        $data['othercom'] = $request->input('othercom');

        //更新T59tb
        T59tb::find($training_process_id)->update($data);
        // 更新T60tb
        $ans = $request->input('ans');

        if (is_array($ans)) {
            foreach ($ans as $id => $va) {
                T60tb::where('id', $id)->update(['ans' => $va]);
            }
        }

        return back()->with('result', '1')->with('message', '儲存成功!');
    }

    /**
     * 刪除處理
     *
     * @param $training_process_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($training_process_id)
    {
        if ($training_process_id) {
            // 取得舊資料
            $oldData = T59tb::where('id', $training_process_id)->first();
            // 刪除t59tb
            T59tb::where('id', $training_process_id)->delete();
            // 刪除T60tb
            T60tb::where('class', $oldData->class)->where('term', $oldData->term)->where('serno', $oldData->serno)->delete();

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

        $data = DB::select('SELECT term FROM t53tb WHERE class=\''.$class.'\' AND times=\'\' ORDER BY term');

        $result = '';

        foreach ($data as $va) {
            $result .= '<option value="'.$va->term.'"';
            $result .= ($selected == $va->term)? ' selected>' : '>';
            $result .= $va->term.'</option>';
        }

        return $result;
    }

    /**
     * 取得課程
     *
     * @param Request $request
     * @return string
     */
    public function getCourse(Request $request)
    {
        $class = $request->input('classes');

        $term = $request->input('term');
        // 取得所有課程
        $course = $this->trainingProcessService->getCourseForCreate($class, $term);

        $result = '';

        foreach ($course as $key => $va) {

            $result .= '
                    <tr>
                        <td>'.($key + 1).'</td>
                        <td>'.$va->classname.'</td>
                        <td><input name="ans['.$va->course.']"  onkeyup="this.value=this.value.replace(/[^\d]/g,\'\')" maxlength="1"></td>
                    </tr>';
        }

        return $result;
    }
}
