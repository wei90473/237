<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\EffectivenessProcessService;
use App\Services\EmployService;
use App\Services\User_groupService;
use App\Models\T95tb;
use App\Models\T01tb;
use App\Models\T53tb;
use App\Models\T54tb;
use App\Models\T55tb;
use App\Models\T56tb;
use DB;


class EffectivenessProcessController extends Controller
{
    /**
     * EffectivenessProcessController constructor.
     * @param EffectivenessProcessService $effectivenessProcessService
     */
    public function __construct(EffectivenessProcessService $effectivenessProcessService ,EmployService $employservice, User_groupService $user_groupService)
    {
        $this->effectivenessProcessService = $effectivenessProcessService;
        $this->employService = $employservice;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('effectiveness_process', $user_group_auth)){
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
        $this_yesr = date('Y') - 1911;
        if (null == $request->get('yerly')) {
            $queryData['yerly'] = $this_yesr;
        } else {
            $queryData['yerly'] = $request->input('yerly');
        }
        $sponsor = $this->employService->getSponsor();
        $this_yesr = date('Y') - 1911;
        if (null == $request->get('yerly')) {
            $queryData['yerly'] = $this_yesr;
        } else {
            $queryData['yerly'] = $request->input('yerly');
        }
        //dd($request->input());
        //班號
        $queryData['class'] = $request->input('class');
        $queryData['name'] = $request->input('name');

        // 分班名稱**
        $queryData['class_branch_name'] = $request->get('class_branch_name');
        // 期別
        $queryData['term'] = $request->input('term');
        // 辦班院區
        $queryData['branch'] = $request->input('branch');
        // 班別類型
        $queryData['process'] = $request->input('process');

        $queryData['sponsor'] = $request->input('sponsor');
        // 訓練性質
        $queryData['traintype'] = $request->input('traintype');
        // 班別性質
        $queryData['type'] = $request->input('type');
        $queryData['sitebranch'] = $request->get('sitebranch');
        $queryData['categoryone'] = $request->get('categoryone');
        $queryData['sdate'] = $request->input('sdate');
        $queryData['edate'] = $request->input('edate');
        $queryData['sdate2'] = $request->input('sdate2');
        $queryData['edate2'] = $request->input('edate2');
        $queryData['sdate3'] = $request->input('sdate3');
        $queryData['edate3'] = $request->input('edate3');
        $queryData['search'] = $request->input('search');
        //dd($request->get('type'));
        //dd($queryData);
        /*// 取得班別
        $queryData['class'] = $request->get('class');
        // 取得期別
        $queryData['term'] = $request->get('term');
        // 取得第幾次調查
        $queryData['times'] = $request->get('times');
        */
        // 排序欄位
        $queryData['_sort_field'] = $request->input('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->input('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
        // 取得列表資料

        $data = array();

        if ($request->all()){
            $data = $this->effectivenessProcessService->getEffectivenessProcessList($queryData);
        }else{
            $sess = $request->session()->get('lock_class');
            if($sess){
              $queryData2['class'] = $sess['class'];
              $queryData2['term'] = $sess['term'];
              $queryData2['yerly'] = substr($sess['class'], 0, 3);
              $data = $this->effectivenessProcessService->getEffectivenessProcessList($queryData2);
            }else{
                $queryData2['class'] = 'none';
                $data = $this->effectivenessProcessService->getEffectivenessProcessList($queryData2);
            }
        }
        // dd($queryData);
        $classList = $this->effectivenessProcessService->getClassList();

        return view('admin/effectiveness_process/list', compact('data', 'queryData', 'classList','sponsor'));
    }

    /**
     * 新增頁
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create($class_info)
    {
        $class_info=unserialize($class_info);


        $list = array();
        $class_list = $this->effectivenessProcessService->getClass($class_info);
        if(empty($class_list->toArray()['data'])){
            $class_list=$this->effectivenessProcessService->getTeacherAndCourse($class_info);
        }
        //dd($class_list->toArray());

        return view('admin/effectiveness_process/form_create', compact('class_info', 'class_list'));
    }

    public function calculate($class_info)
    {
        $class_info=unserialize($class_info);

        $this->effectivenessProcessService->cleanOkRate($class_info);
        $this->effectivenessProcessService->insertOkRate($class_info);
        $this->effectivenessProcessService->delete_insert_t57tb_data($class_info);
        $this->effectivenessProcessService->insert_t57tb_data($class_info);

        return back()->with('result', '1')->with('message', '問卷統計計算完成!');
    }

    //年統計
    public function year_calculate(Request $request)
    {
        $class_info=[];
        $class_info['year']=$request->input('year');
        $this->effectivenessProcessService->cleanOkRate($class_info);
        $this->effectivenessProcessService->insertOkRate($class_info);
        $this->effectivenessProcessService->delete_insert_t57tb_data($class_info);
        $this->effectivenessProcessService->insert_t57tb_data($class_info);

        return back()->with('result', '1')->with('message', "{$class_info['year']}問卷統計計算完成!");
    }

    /**
     * 新增處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // 取得更新時間
        $time = date('Y-m-d H:i:s');

        $data['class'] = $request->input('class');
        $data['term'] = $request->input('term');
        $data['times'] = $request->input('times');
        $data['q11'] = $request->input('q11');
        $data['q12'] = $request->input('q12');
        $data['q13'] = $request->input('q13');
        $data['q14'] = $request->input('q14');
        $data['q15'] = $request->input('q15');
        $data['q21'] = $request->input('q21');
        $data['q22'] = $request->input('q22');
        $data['q23'] = $request->input('q23');
        $data['q31'] = $request->input('q31');
        $data['q32'] = $request->input('q32');
        $data['q33'] = $request->input('q33');
        $data['q41'] = $request->input('q41');
        $data['q42'] = $request->input('q42');
        $data['note'] = $request->input('note');
        $data['fillmk'] = 1;
        $data['crtdate'] = $time;
        $data['upddate'] = $time;

        // 檢查
        if ( ! T53tb::where('class', $data['class'])->where('term', $data['term'])->where('times', $data['times'])->exists()) {
            return back()->with('result', '0')->with('message', '未設定問卷題目!');
        }


        // 取得編號
        $serno = T95tb::where('class', $data['class'])->where('term', $data['term'])->where('times', $data['times'])->max('serno') +1;
        $serno = str_pad($serno ,3,'0',STR_PAD_LEFT);

        $data['serno'] = $serno;
        // 新增t95
        $result = T95tb::create($data);

        //$t56Data = $request->input('t56');

        $input_t56tb=$request->input();
        $final_insert=[];
        foreach($input_t56tb as $key => $insert){
            $info=explode("_",$key);
            if(count($info)>2){
                $final_insert['class']=$data['class'];
                $final_insert['term']=$data['term'];
                $final_insert['times']=$data['times'];
                $final_insert['course']=$info[2];
                $final_insert['serno']=$serno;
                $final_insert['idno']=$info[1];
                $final_insert['ans1']=$insert[0];
                $final_insert['ans2']=$insert[1];
                $final_insert['ans3']=$insert[2];
                $final_insert['crtdate'] = $time;
                $final_insert['upddate'] = $time;
                $final_insert['fillmk'] = 1;
                T56tb::create($final_insert);
            }
        }
        //var_dump($final_insert);
        //die();

        $arr=['class'=>$data['class'],'term'=>$data['term'],'times'=>$data['times']];
        $arr=serialize($arr);
        return redirect("/admin/effectiveness_process/{$arr}")->with('result', '1')->with('message', '新增成功!');
    }

    /**
     * 顯示頁
     *
     * @param $effectiveness_process_id
     */
    public function show($effectiveness_process_id)
    {
        return $this->edit($effectiveness_process_id);
    }

    /**
     * 編輯頁
     *
     * @param $effectiveness_process_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($effectiveness_process_id)
    {
        //dd($effectiveness_process_id);
        $keyArr = unserialize($effectiveness_process_id);

        $data = DB::select('SELECT * FROM t95tb WHERE class = \''.$keyArr['class'].'\' and term = \''.$keyArr['term'].'\' and times = \''.$keyArr['times'].'\'');

        /*if (!$data) {
            return view('admin/errors/error');
        }*/
        if(empty($data)){
            $data[0] = new \stdClass();
            $data[0]->class=$keyArr['class'];
            $data[0]->term=$keyArr['term'];
            $data[0]->times=$keyArr['times'];
            $classData = T01tb::select('class', 'name')->where('class', $data[0]->class)->first();
            unset($data[0]);
        }else{
            $classData = T01tb::select('class', 'name')->where('class', $data[0]->class)->first();
        }
        $test = $this->effectivenessProcessService->getClass($keyArr);
        //dd($test);
        // 每頁幾筆
        //$queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 1;
        $queryData['_paginate_qty'] = 1;
        return view('admin/effectiveness_process/form', compact('data', 'classData', 'keyArr','queryData','test'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $effectiveness_process_id)
    {

        // 取得POST資料
        $data['q11'] = $request->input('q11');
        $data['q12'] = $request->input('q12');
        $data['q13'] = $request->input('q13');
        $data['q14'] = $request->input('q14');
        $data['q15'] = $request->input('q15');
        $data['q21'] = $request->input('q21');
        $data['q22'] = $request->input('q22');
        $data['q23'] = $request->input('q23');
        $data['q31'] = $request->input('q31');
        $data['q32'] = $request->input('q32');
        $data['q33'] = $request->input('q33');
        $data['q41'] = $request->input('q41');
        $data['q42'] = $request->input('q42');
        $data['note'] = $request->input('note');

        $keyArr = explode('-',$effectiveness_process_id);

        // 更新T95tb
        T95tb::where('class', $keyArr[0])->where('term', $keyArr[1])->where('times', $keyArr[2])->where('serno', $keyArr[3])->update($data);

        $input_t56tb=$request->input();

        $final_insert=[];
        foreach($input_t56tb as $key => $insert){
            $info=explode("_",$key);
            if(count($info)>2){
                //$final_insert['class']=$data['class'];
                //$final_insert['term']=$data['term'];
                //$final_insert['times']=$data['times'];
                //$final_insert['course']=$info[2];
                //$final_insert['serno']=$serno;
                //$final_insert['idno']=$info[1];
                $final_insert['ans1']=$insert[0];
                $final_insert['ans2']=$insert[1];
                $final_insert['ans3']=$insert[2];
                //$final_insert['crtdate'] = $time;
                //$final_insert['upddate'] = $time;
                //$final_insert['fillmk'] = 1;
                T56tb::where('class', $keyArr[0])->where('term', $keyArr[1])->where('times', $keyArr[2])->where('serno', $keyArr[3])
                        ->where('course',$info[2])->update($final_insert);
            }

        }


        return back()->with('result', '1')->with('message', '儲存成功!');
    }

    /**
     * 刪除處理
     *
     * @param $effectiveness_process_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($effectiveness_process_id)
    {
        $class_info=unserialize($effectiveness_process_id);
        if($class_info){
            // 刪除T95
            T95tb::where('class',$class_info['class'])->where('term',$class_info['term'])->where('times',$class_info['times'])
                    ->where('serno',$class_info['serno'])->delete();
            // 刪除t56
            T56tb::where('class', $class_info['class'])->where('term', $class_info['term'])->where('times', $class_info['times'])
                    ->where('serno', $class_info['serno'])->delete();
            unset($class_info['serno']);
            $class_info=serialize($class_info);
            //return redirect()->action('\App\Http\Controllers\EffectivenessProcessController@edit', ['effectiveness_process_id' => $class_info])
            //->with('result', '1')->with('message', '刪除成功!');
            return redirect("/admin/effectiveness_process/{$class_info}/edit")->with('result', '1')->with('message', '刪除成功!');

        }else{

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

        $data = DB::select(' SELECT DISTINCT term FROM t53tb  WHERE class = "'.$class.'" AND times != "" ORDER BY term ');

        $result = '';

        foreach ($data as $va) {
            $result .= '<option value="'.$va->term.'"';
            $result .= ($selected == $va->term)? ' selected>' : '>';
            $result .= $va->term.'</option>';
        }

        return $result;
    }

    /**
     * 取得第幾次調查
     *
     * @param Request $request
     * @return string
     */
    public function getTimes(Request $request)
    {
        $class = $request->input('classes');

        $trem = $request->input('term');

        $selected = $request->input('selected');

        $data = DB::select(' SELECT times FROM t53tb WHERE class = "'.$class.'"
                    AND term = "'.$trem.'"
                    AND times != ""
                    ORDER BY times ');

        $result = '';

        foreach ($data as $va) {
            $result .= '<option value="'.$va->times.'"';
            $result .= ($selected == $va->times)? ' selected>' : '>';
            $result .= $va->times.'</option>';
        }

        return $result;
    }

    /**
     * 取得講座方面
     *
     * @return string
     */
    public function getList(Request $request)
    {
        $class = $request->input('classes');

        $trem = $request->input('term');

        $times = $request->input('times');

        $sql = "SELECT A.sequence AS no,
            C.name AS course_name,
            D.cname AS teacher,
            A.class,A.term,A.times,A.course,A.idno
            FROM t54tb A
            INNER JOIN t06tb C
            ON A.class=C.class
            AND A.term=C.term
            AND A.course=C.course
            INNER JOIN m01tb D
            ON A.idno=D.idno

            WHERE A.class = '".$class."'
            AND  A.term= '".$trem."'
            AND  A.times= '".$times."'
            ORDER BY A.sequence";

        $list = DB::select($sql);

        $result = '';

        foreach ($list as $key => $va) {

            $result .= '<tr>';
            $result .= '    <td>'.$va->no.'</td>';
            $result .= '    <td>'.$va->course_name.'</td>';
            $result .= '    <td>'.$va->teacher.'</td>';
            $result .= '    <td><input name="t56['.$va->course.'_'.$va->idno.'][ans1]" value=""></td>';
            $result .= '    <td><input name="t56['.$va->course.'_'.$va->idno.'][ans2]" value=""></td>';
            $result .= '    <td><input name="t56['.$va->course.'_'.$va->idno.'][ans3]" value=""></td>';
            $result .= '</tr>';
        }

        return $result;
    }
}
