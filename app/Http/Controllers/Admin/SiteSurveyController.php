<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\SiteSurveyService;
use App\Services\User_groupService;
use App\Models\T73tb;


class SiteSurveyController extends Controller
{
    /**
     * SiteSurveyController constructor.
     * @param SiteSurveyService $siteSurveyService
     */
    public function __construct(SiteSurveyService $siteSurveyService, User_groupService $user_groupService)
    {
        $this->siteSurveyService = $siteSurveyService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('site_survey', $user_group_auth)){
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
        // 取得年份
        $queryData['year'] = $request->get('year');
        // 第幾次調查
        $queryData['times'] = $request->get('times');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 取得列表資料
        $data = $this->siteSurveyService->getSiteSurveyList($queryData);

        return view('admin/site_survey/list', compact('data', 'queryData'));
    }

    /**
     * 新增頁
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin/site_survey/form');
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
        $data['year'] = $request->input('year');
        $data['times'] = $request->input('times');
        $data['q1'] = $request->input('q1');
        $data['q2'] = $request->input('q2');
        $data['q3'] = $request->input('q3');
        $data['q4'] = $request->input('q4');
        $data['q5'] = $request->input('q5');
        $data['q6'] = $request->input('q6');
        $data['dept'] = $request->input('dept');
        $data['extdept'] = $request->input('extdept');
        $data['site1'] = $request->input('site1');
        $data['site2'] = $request->input('site2');
        $data['site3'] = $request->input('site3');
        $data['comment'] = $request->input('comment');

        // 取得編號
        $data['serno'] = T73tb::where('year', $data['year'])->where('times', $data['times'])->max('serno') + 1;
        // 編號補足三位數
        $data['serno'] = str_pad($data['serno'] ,3,'0',STR_PAD_LEFT);

        //新增
        $result = T73tb::create($data);

        return redirect('/admin/site_survey/'.$result->id)->with('result', '1')->with('message', '新增成功!');
    }

    /**
     * 顯示頁
     *
     * @param $site_survey_id
     */
    public function show($site_survey_id)
    {
        return $this->edit($site_survey_id);
    }

    /**
     * 編輯頁
     *
     * @param $site_survey_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($site_survey_id)
    {
        $data = T73tb::find($site_survey_id);

        if ( ! $data) {

            return view('admin/errors/error');
        }

        return view('admin/site_survey/form', compact('data'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $site_survey_id)
    {
        // 取得POST資料
        $data['q1'] = $request->input('q1');
        $data['q2'] = $request->input('q2');
        $data['q3'] = $request->input('q3');
        $data['q4'] = $request->input('q4');
        $data['q5'] = $request->input('q5');
        $data['q6'] = $request->input('q6');
        $data['dept'] = $request->input('dept');
        $data['extdept'] = $request->input('extdept');
        $data['site1'] = $request->input('site1');
        $data['site2'] = $request->input('site2');
        $data['site3'] = $request->input('site3');
        $data['comment'] = $request->input('comment');

        //更新
        T73tb::find($site_survey_id)->update($data);

        return back()->with('result', '1')->with('message', '儲存成功!');

    }

    /**
     * 刪除處理
     *
     * @param $site_survey_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($site_survey_id)
    {
        if ($site_survey_id) {

            T73tb::find($site_survey_id)->delete();

            return back()->with('result', '1')->with('message', '刪除成功!');

        } else {

            return back()->with('result', '0')->with('message', '刪除失敗!');
        }
    }

    /**
     * 取得第幾次調查
     *
     * @param $year
     * @return string
     */
    public function getTimes($year)
    {
        $data = T73tb::select('times')->where('year', $year)->where('times', '!=', '')->groupBy('times')->get();

        $result = '';

        foreach ($data as $va) {
            $result .= '<option value="'.$va->times.'" selected>';
            $result .= $va->times.'</option>';
        }

        return $result;
    }
}
