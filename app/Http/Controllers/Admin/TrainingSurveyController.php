<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\TrainingSurveyService;
use App\Models\T01tb;
use App\Models\T53tb;
use App\Models\T58tb;
use App\Models\T59tb;
use App\Models\T60tb;
use DB;


class TrainingSurveyController extends Controller
{
    /**
     * TrainingSurveyController constructor.
     * @param TrainingSurveyService $trainingSurveyService
     */
    public function __construct(TrainingSurveyService $trainingSurveyService)
    {
        $this->trainingSurveyService = $trainingSurveyService;
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
        $data = $this->trainingSurveyService->getTrainingSurveyList($queryData);
        // 取得課程列表
        $classList = $this->trainingSurveyService->getClassList();

        return view('admin/training_survey/list', compact('data', 'queryData', 'classList'));
    }

    /**
     * 新增頁
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        // 取得課程列表
        $classList = $this->trainingSurveyService->getClassList();

        return view('admin/training_survey/form', compact('classList'));
    }

    /**
     * 新增處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data['class'] = $request->input('class');
        $data['term'] = $request->input('term');
        $data['copy'] = $request->input('copy');
        $data['times'] = '';
        $data['fillsdate'] = '';
        $data['filledate'] = '';
        $data['upddate'] = date('Y-m-d H:i:s');

        // 檢查是否重複
        if (T53tb::where('class', mb_substr($data['class'], 0, 6))->where('term', $data['term'])->where('times', '')->exists()) {

            return back()->with('result', '0')->with('message', '此問卷已存在!');
        }

        // 新增T53表
        $result = T53tb::create($data);

        // 新增新資料
        $course = $request->input('course');

        if (is_array($course)) {
            foreach ($course as $key => $va) {

                $newData = array();
                $newData['class'] = $data['class'];
                $newData['term'] = $data['term'];
                $newData['course'] = $va;
                $newData['sequence'] = $key + 1;

                T58tb::create($newData);
            }
        }

        return redirect('/admin/training_survey/'.$result->id)->with('result', '1')->with('message', '新增成功!');
    }

    /**
     * 顯示頁
     *
     * @param $training_survey_id
     */
    public function show($training_survey_id)
    {
        return $this->edit($training_survey_id);
    }

    /**
     * 編輯頁
     *
     * @param $training_survey_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($training_survey_id)
    {
        $data = T53tb::find($training_survey_id);

        if ( ! $data) {

            return view('admin/errors/error');
        }

        $classData = T01tb::select('name', 'class')->where('class', $data->class)->first();
        // 已選取的課程
        $course = $this->trainingSurveyService->getCourseSelectList($data->class, $data->term);
        // 未選取的課程
        $courseNotSelect = $this->trainingSurveyService->getCourseNotSelectList($data->class, $data->term);

        return view('admin/training_survey/form', compact('data', 'classData', 'course', 'courseNotSelect'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $training_survey_id)
    {
        // 取得舊資料
        $oldData = T53tb::find($training_survey_id);
        // 此問卷是否已有答案資料存在
        if (T59tb::where('class', mb_substr($oldData->class, 0, 6))->where('term', $oldData->term)->exists()) {

            return back()->with('result', '0')->with('message', '此問卷已有答案資料存在!');
        }
        // 更新t53tb
        T53tb::where('id', $training_survey_id)->update(['copy' => $request->input('copy')]);
        // 刪除舊資料
        T58tb::where('class', $oldData->class)->where('term', $oldData->term)->delete();
        // 新增新資料
        $course = $request->input('course');

        if (is_array($course)) {
            foreach ($course as $key => $va) {

                $newData = array();
                $newData['class'] = $oldData->class;
                $newData['term'] = $oldData->term;
                $newData['course'] = $va;
                $newData['sequence'] = $key + 1;

                T58tb::create($newData);
            }
        }

        return back()->with('result', '1')->with('message', '儲存成功!');
    }

    /**
     * 刪除處理
     *
     * @param $training_survey_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($training_survey_id)
    {
        if ($training_survey_id) {

            $data = T53tb::find($training_survey_id);

            T58tb::where('class', $data->class)->where('term', $data->term)->delete();
            T59tb::where('class', $data->class)->where('term', $data->term)->delete();
            T60tb::where('class', $data->class)->where('term', $data->term)->delete();
            T53tb::where('class', $data->class)->where('term', $data->term)->where('times', '')->delete();

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

        if (is_numeric( mb_substr($class, 0, 1))) {

            $data = DB::select('SELECT DISTINCT term FROM t04tb WHERE class = \''.$class.'\' ORDER BY `term`');
        } else {

            $data = DB::select('SELECT DISTINCT serno as term FROM t38tb WHERE meet = \''.$class.'\' ORDER BY `term`');
        }

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
        $course = $this->trainingSurveyService->getCourseAllList($class, $term);

        $result = '';

        foreach ($course as $key => $va) {

            $result .= '<div class="checkbox checkbox-primary">
                            <input id="course'.$key.'" name="course[]" value="'.$va->course.'" type="checkbox">
                            <label for="course'.$key.'">
                                '.$this->showDate($va->date).' '.$va->coursename.' '.$va->cname.'
                            </label>
                            <i class="fa fa-arrow-up pointer text-secondary" onclick="prev(this);"></i>
                            <i class="fa fa-arrow-down pointer text-secondary" onclick="next(this);"></i>
                        </div>';
        }

        return $result;
    }

    /**
     * 顯示日期格式
     *
     * @param $date
     * @return string
     */
    public function showDate($date)
    {
        return ($date)? mb_substr($date, 0, 3).'/'.mb_substr($date, 3, 2).'/'.mb_substr($date, 5, 2) : '';
    }
}
