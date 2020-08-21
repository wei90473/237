<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\EmployService;
use App\Services\Term_processService;
use App\Services\User_groupService;
use App\Models\T09tb;
use App\Models\T08tb;
use App\Models\T04tb;
use App\Models\T06tb;
use App\Models\M01tb;
use App\Models\S02tb;
use App\Models\Employ_sort;
use DB;


class EmployController extends Controller
{
    /**
     * EmployController constructor.
     * @param EmployService $employService
     */
    public function __construct(EmployService $employService, Term_processService $term_processService, User_groupService $user_groupService)
    {
        $this->employService = $employService;
        $this->term_processService = $term_processService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('employ', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
        });
        setProgid('employ');
        DB::connection()->enableQueryLog(); //啟動SQL_LOG
    }

    /**
     * 列表頁
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $sponsor = $this->employService->getSponsor();
        //年
        $this_yesr = date('Y') - 1911;
        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($queryData['year_list']);
        // echo "\n</pre>\n";
        if(null == $request->get('yerly')){
            $queryData['yerly'] = $this_yesr;
        }else{
            $queryData['yerly'] = $request->get('yerly');
        }
        //班號
        $queryData['class'] = $request->get('class');
        $queryData['name'] = $request->get('name');

        // 分班名稱**
        $queryData['class_branch_name'] = $request->get('class_branch_name');
        // 期別
        $queryData['term'] = $request->get('term');
        // 辦班院區
        $queryData['branch'] = $request->get('branch');
        // 班別類型
        $queryData['process'] = $request->get('process');

        $queryData['sponsor'] = $request->get('sponsor');
        // 訓練性質
        $queryData['traintype'] = $request->get('traintype');
        // 班別性質
        $queryData['type'] = $request->get('type');
        $queryData['sitebranch'] = $request->get('sitebranch');
        $queryData['categoryone'] = $request->get('categoryone');
        $queryData['sdate'] = $request->get('sdate');
        $queryData['edate'] = $request->get('edate');
        $queryData['sdate2'] = $request->get('sdate2');
        $queryData['edate2'] = $request->get('edate2');
        $queryData['sdate3'] = $request->get('sdate3');
        $queryData['edate3'] = $request->get('edate3');

        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;


		$queryData['search'] = $request->get('search');

        if($queryData['search'] != 'search' ){
            $sess = $request->session()->get('lock_class');
            if($sess){
              $queryData2['class'] = $sess['class'];
              $queryData2['term'] = $sess['term'];
              $queryData2['yerly'] = substr($sess['class'], 0, 3);
              $data = $this->employService->getEmployList($queryData2);
              return view('admin/employ/list', compact('data', 'queryData', 'sponsor'));
            }
			$queryData2['class'] = 'none';
			$data = $this->employService->getEmployList($queryData2);
		}else{
            if(!empty($queryData['term'])){
                $queryData['term'] = str_pad($queryData['term'],2,'0',STR_PAD_LEFT);
              }
            $data = $this->employService->getEmployList($queryData);
        }




      //  $classList = T01tb::select('class', 'name')->get();
        return view('admin/employ/list', compact('data', 'queryData', 'sponsor'));
    }

    public function detail(Request $request)
    {
      if(null == $request->get('hire')){
        $queryData['hire'] = '';
      }
      $queryData['class'] = $request->get('class');
        $queryData['term'] = $request->get('term');

        $class_data = $this->employService->getClass($queryData);

        $teacher_list = $this->employService->getDetailList($queryData);

        return view('admin/employ/detail', compact('data', 'class_data', 'teacher_list'));
    }

  /**
     * 講課酬勞查詢
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function payroll(Request $request)
    {
        //起始日
        $queryData['sdate'] = $request->get('sdate');
        //結束日
        $queryData['edate'] = $request->get('edate');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;


        if($request->get('search') != 'search')
        {
            $data = array();
        }else{
            if(empty($queryData['sdate']) || empty($queryData['edate'])){
                return back()->with('result', '0')->with('message', '請輸入日期');
            }else{

                $query = T06tb::select('t09tb.idno', DB::raw('SUM(teachtot) as total'));
                $data = $query->join('t09tb', function($join)
                        {
                            $join->on('t09tb.class', '=', 't06tb.class')
                            ->on('t09tb.term', '=', 't06tb.term')
                            ->on('t09tb.course', '=', 't06tb.course');
                        })
                         ->where('date', '<=', $queryData['edate'])
                         ->where('date', '>=', $queryData['sdate'])
                         ->groupBy('t09tb.idno')
                         ->orderBy('total', 'desc')
                         ->get()->toArray();

                foreach($data as & $row){
                    $query = M01tb::select('m01tb.cname');
                    $name_data = $query->where('idno', $row['idno'])
                             ->get()->toArray();
                    $row['name'] = $name_data[0]['cname'];
                    if(empty($row['total'])){
                        $row['total'] = '0';
                    }
                }
                // echo '<pre style="text-align:left;">' . "\n";
                // print_r($data);
                // echo "\n</pre>\n";
                // die();
            }
        }

        return view('admin/employ/payroll', compact('data', 'queryData'));
    }

    public function sort(Request $request)
    {
    	$queryData['class'] = $request->get('class');
        $queryData['term'] = $request->get('term');

        $data = $this->employService->getTeacher($queryData);

        if(empty($data)){
            return redirect("/admin/employ/detail?class={$queryData['class']}&term={$queryData['term']}")->with('result', '0')->with('message', '目前沒有可設定的講座');
        }

    	return view('admin/employ/sort', compact('data', 'queryData'));
    }

    public function updateSort(Request $request)
    {
        $data = $request->all();
        $queryData['class'] = $request->get('class');
        $queryData['term'] = $request->get('term');
        unset($data['_token']);
        unset($data['class']);
        unset($data['term']);

        if(empty($queryData['class']) || empty($queryData['term'])){
            return redirect("/admin/employ/detail?class={$queryData['class']}&term={$queryData['term']}")->with('result', '0')->with('message', '操作錯誤');
        }

        if(checkNeedModifyLog('employ')){
            $olddata = Employ_sort::where('class', $queryData['class'])->where('term', $queryData['term'])->get()->toarray();
        }

        Employ_sort::where('class', $queryData['class'])->where('term', $queryData['term'])->delete();

        $sql = DB::getQueryLog();
	    if(checkNeedModifyLog('employ')){
	        createModifyLog('D','Employ_sort',$olddata,'',end($sql));
	    }

        //dd($data);
        foreach($data as $key => $row){
            if(!empty($row)){
                $idno = substr($key, 0, -5);
                $fields = array(
                    'class' => $queryData['class'],
                    'term' => $queryData['term'],
                    'idno' => $idno,
                    'teacher_sort' => $row,
                );
                $result = Employ_sort::create($fields);
                $sql = DB::getQueryLog();
                if(checkNeedModifyLog('employ')){
                    $nowdata = Employ_sort::where('class', $queryData['class'])->where('term', $queryData['term'])->where('idno', $idno)->get()->toarray();
                    createModifyLog('I','Employ_sort','',$nowdata,end($sql));
                }
            }

        }
        // dd($data);
        //return back()->with('result', '1')->with('message', '儲存成功!');
        return redirect("/admin/employ/detail?class={$queryData['class']}&term={$queryData['term']}")->with('result', '1')->with('message', '設定成功!');
    }

    /**
     * 新增頁
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        $queryData['class'] = $request->get('class');
        $queryData['term'] = $request->get('term');

        $class_data = $this->employService->getClass($queryData);

        return view('admin/employ/form', compact('class_data'));
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

        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('employ', $data['class'], $data['term']);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法新增');
        }

        $idnoData = M01tb::where('idno', $data['idno'])->first();

        $query = S02tb::select('deductrate1', 'deductrate2', 'insurerate');
        $data3 = $query->distinct()->get()->toArray();
        $data3 = $data3[0];

        $query = T06tb::select('date');
        $data4 = $query->where('class', $data['class'])
                 ->where('term', $data['term'])
                 ->where('course', $data['course'])
                 ->get()->toArray();
        $data4 = $data4[0];

        if(empty($data4['date'])){
            $query = T04tb::select('t04tb.sdate');
            $results = $query->where('class', $data['class'])->where('term', $data['term'])->get()->toArray();
            $date = $results[0]['sdate'];
        }else{
            $date = $data4['date'];
        }

        if(in_array($idnoData->idkind, array('3', '4', '7'))){

            $deductamt1 = ($data['lectamt'] + $data['speakamt'] + $data['review_total'] + $data['other_salary']) * ($data3['deductrate2'] / 100);
            if($data['no_tax'] == 'Y'){
                $deductamt1 = 0;
            }
            if($data['noteamt'] > 5000){
                $deductamt2 = $data['noteamt'] * 0.2;
            }else{
                $deductamt2 = 0;
            }

        }else{
            $deductamt1 = ($data['lectamt'] + $data['speakamt'] + $data['review_total'] + $data['other_salary']) * ($data3['deductrate1'] / 100);
            if($data['noteamt'] > 5000){
                $deductamt2 = $data['noteamt'] * 0.1;
            }else{
                $deductamt2 = 0;
            }

        }
        $data['deductamt'] = $deductamt1 + $deductamt2;
        $data['deductamt1'] = $deductamt1;
        $data['deductamt2'] = $deductamt2;
        $data['insureamt1'] = '0';
        $data['insureamt2'] = '0';
        $data['insuremk1'] = 'Y';
        $data['insuremk2'] = 'Y';

        if($data['kind'] == '3'){
            $data['insuremk1'] = 'N';
            $data['insuremk2'] = 'N';
        }

        if( isset($idnoData->insurekind1) && in_array($idnoData->kind, array('1', '2', '4'))){
            if($idnoData->insurekind1 == 'Y'){
                $data['insuremk1'] = 'N';
            }else{
                $data['insuremk1'] = 'Y';
            }

            if($idnoData->insurekind2 == 'Y'){
                $data['insuremk2'] = 'N';
            }else{
                $data['insuremk2'] = 'Y';
            }
        }

        if($data['insuremk1'] == 'Y'){
            if($date >= "1030901" && $date <= "1040630"){
                if(($data['lectamt'] + $data['speakamt'] + $data['review_total'] + $data['other_salary']) > 19273){
                    $data['insureamt1'] = ($data['lectamt'] + $data['speakamt'] + $data['review_total'] + $data['other_salary']) * $data3['insurerate'];
                }
            }else if($date >= "1040701"){
                if(($data['lectamt'] + $data['speakamt'] + $data['review_total'] + $data['other_salary']) >= 20008){
                    $data['insureamt1'] = ($data['lectamt'] + $data['speakamt'] + $data['review_total'] + $data['other_salary']) * $data3['insurerate'];
                }
            }else{
                if(($data['lectamt'] + $data['speakamt'] + $data['review_total'] + $data['other_salary']) > 5000){
                    $data['insureamt1'] = ($data['lectamt'] + $data['speakamt'] + $data['review_total'] + $data['other_salary']) * $data3['insurerate'];
                }
            }
        }
        if($data['insuremk2'] == 'Y'){
            if($date >= "1050101"){
                if($data['noteamt'] >= 20000){
                    $data['insureamt2'] = $data['noteamt'] * $data3['insurerate'];
                }
            }else{
                if($data['noteamt'] > 5000){
                    $data['insureamt2'] = $data['noteamt'] * $data3['insurerate'];
                }
            }
        }

        $data['insuretot'] = $data['insureamt1'] + $data['insureamt2'];

        $data['netpay'] = $data['teachtot']-$data['deductamt']-$data['insuretot'];

        $data['totalpay'] = $data['netpay'] + $data['tratot'];

        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($data);
        // echo "\n</pre>\n";
        // die();

        //新增
        $result = T09tb::create($data);

        $sql = DB::getQueryLog();
        if(checkNeedModifyLog('employ')){
            $nowdata = T09tb::where('class', $data['class'])->where('term', $data['term'])->where('idno', $data['idno'])->get()->toarray();
            createModifyLog('I','T09tb','',$nowdata,end($sql));
        }

        return redirect('/admin/employ/'.$result->id)->with('result', '1')->with('message', '新增成功!');
    }

    /**
     * 顯示頁
     *
     * @param $id
     */
    public function show($id)
    {
        return $this->edit($id);
    }

    /**
     * 編輯頁
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        $data = T09tb::find($id);

        $queryData['class'] = $data->class;
        $queryData['term'] = $data->term;

        $class_data = $this->employService->getClass($queryData);

        if ( ! $data) {

            return view('admin/errors/error');
        }

        $idnoData = M01tb::where('idno', $data->idno)->first();

        return view('admin/employ/form', compact('data', 'idnoData', 'class_data'));
    }

    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();

        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('employ', $data['class'], $data['term']);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法修改');
        }

        $T09tb_data = T09tb::find($id);
        $idnoData = M01tb::where('idno', $T09tb_data->idno)->first();

        $query = S02tb::select('deductrate1', 'deductrate2', 'insurerate');
        $data3 = $query->distinct()->get()->toArray();
        $data3 = $data3[0];

        $query = T06tb::select('date');
        $data4 = $query->where('class', $T09tb_data->class)
                 ->where('term', $T09tb_data->term)
                 ->where('course', $T09tb_data->course)
                 ->get()->toArray();
        $data4 = $data4[0];

        if(empty($data4['date'])){
            $query = T04tb::select('t04tb.sdate');
            $results = $query->where('class', $T09tb_data->class)->where('term', $T09tb_data->term)->get()->toArray();
            $date = $results[0]['sdate'];
        }else{
            $date = $data4['date'];
        }

        if(in_array($idnoData->idkind, array('3', '4', '7'))){

            $deductamt1 = ($data['lectamt'] + $data['speakamt'] + $data['review_total'] + $data['other_salary']) * ($data3['deductrate2'] / 100);
            if($data['no_tax'] == 'Y'){
                $deductamt1 = 0;
            }
            if($data['noteamt'] > 5000){
                $deductamt2 = $data['noteamt'] * 0.2;
            }else{
                $deductamt2 = 0;
            }

        }else{
            $deductamt1 = ($data['lectamt'] + $data['speakamt'] + $data['review_total'] + $data['other_salary']) * ($data3['deductrate1'] / 100);
            if($data['noteamt'] > 5000){
                $deductamt2 = $data['noteamt'] * 0.1;
            }else{
                $deductamt2 = 0;
            }

        }
        $data['deductamt'] = $deductamt1 + $deductamt2;
        $data['deductamt1'] = $deductamt1;
        $data['deductamt2'] = $deductamt2;
        $data['insureamt1'] = '0';
        $data['insureamt2'] = '0';
        $data['insuremk1'] = 'Y';
        $data['insuremk2'] = 'Y';

        if($data['kind'] == '3'){
            $data['insuremk1'] = 'N';
            $data['insuremk2'] = 'N';
        }
        // dd($idnoData);
        if( isset($idnoData->insurekind1) && in_array($idnoData->kind, array('1', '2', '4'))){
            if($idnoData->insurekind1 == 'Y'){
                $data['insuremk1'] = 'N';
            }else{
                $data['insuremk1'] = 'Y';
            }

            if($idnoData->insurekind2 == 'Y'){
                $data['insuremk2'] = 'N';
            }else{
                $data['insuremk2'] = 'Y';
            }
        }

        if($data['insuremk1'] == 'Y'){

            if($date >= "1030901" && $date <= "1040630"){
                if(($data['lectamt'] + $data['speakamt'] + $data['review_total'] + $data['other_salary']) > 19273){
                    $data['insureamt1'] = ($data['lectamt'] + $data['speakamt'] + $data['review_total'] + $data['other_salary']) * $data3['insurerate'];
                }
            }else if($date >= "1040701"){
                if(($data['lectamt'] + $data['speakamt'] + $data['review_total'] + $data['other_salary']) >= 20008){
                    $data['insureamt1'] = ($data['lectamt'] + $data['speakamt'] + $data['review_total'] + $data['other_salary']) * $data3['insurerate'];
                }
            }else{
                if(($data['lectamt'] + $data['speakamt'] + $data['review_total'] + $data['other_salary']) > 5000){
                    $data['insureamt1'] = ($data['lectamt'] + $data['speakamt'] + $data['review_total'] + $data['other_salary']) * $data3['insurerate'];
                }
            }
        }
        if($data['insuremk2'] == 'Y'){
            if($date >= "1050101"){
                if($data['noteamt'] >= 20000){
                    $data['insureamt2'] = $data['noteamt'] * $data3['insurerate'];
                }
            }else{
                if($data['noteamt'] > 5000){
                    $data['insureamt2'] = $data['noteamt'] * $data3['insurerate'];
                }
            }
        }

        if(isset($data['insuremk1']) && $data['insuremk1'] == 'N'){
            $data['insureamt1'] = '0';
        }

        if(isset($data['insuremk2']) && $data['insuremk2'] == 'N'){
            $data['insureamt2'] = '0';
        }

        $data['insuretot'] = $data['insureamt1'] + $data['insureamt2'];

        $data['netpay'] = $data['teachtot']-$data['deductamt']-$data['insuretot'];

        $data['totalpay'] = $data['netpay'] + $data['tratot'];

        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($data);
        // echo "\n</pre>\n";
        // die();
        // dd($data);

        if(checkNeedModifyLog('employ')){
            $olddata = T09tb::where('id', $id)->get()->toarray();
        }

        //更新
        T09tb::find($id)->update($data);

        $sql = DB::getQueryLog();
        if(checkNeedModifyLog('employ')){
            $nowdata = T09tb::where('id', $id)->get()->toarray();
            createModifyLog('U','T09tb',$olddata,$nowdata,end($sql));
        }

        return back()->with('result', '1')->with('message', '儲存成功!');

    }

    /**
     * 刪除處理
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        if ($id) {

            $EditDelete = $this->employService->getEditDelete($id);
            $T09tb_data = T09tb::find($id);

            //班務流程凍結
            $freeze = $this->term_processService->getFreeze('employ', $T09tb_data->class, $T09tb_data->term);
            if($freeze == 'Y'){
                return back()->with('result', 0)->with('message', '凍結中無法刪除');
            }

            if($EditDelete['EditorDelete'] == 'Y'){
                $T08 = T08tb::select('t08tb.id')->where('class', $T09tb_data->class)->where('term', $T09tb_data->term)->where('course', $T09tb_data->course)->where('idno', $T09tb_data->idno)->get()->toArray();
                if(!empty($T08[0]['id'])){

                    if(checkNeedModifyLog('employ')){
		                $olddata = T08tb::where('id', $T08[0]['id'])->get()->toarray();
		            }

                    T08tb::find($T08[0]['id'])->delete();

                    $sql = DB::getQueryLog();
		            if(checkNeedModifyLog('employ')){
		                createModifyLog('D','T08tb',$olddata,'',end($sql));
		            }

                }

                if(checkNeedModifyLog('employ')){
		            $olddata = T09tb::where('id', $id)->get()->toarray();
		        }

                T09tb::find($id)->delete();

                $sql = DB::getQueryLog();
		        if(checkNeedModifyLog('employ')){
		            createModifyLog('D','T09tb',$olddata,'',end($sql));
		        }

                return redirect("/admin/employ/detail?class={$T09tb_data->class}&term={$T09tb_data->term}")->with('result', '1')->with('message', '刪除成功!');
            }else{
                return back()->with('result', '0')->with('message', "已作過轉帳{$EditDelete['paidday']}，資料不可修改或刪除!");
            }

        } else {

            return back()->with('result', '0')->with('message', '刪除失敗!');
        }
    }

    /**
     * 取得期別
     *
     * @param $class
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTerm(Request $request)
    {
        $class = $request->input('class');

        $selected = $request->input('selected')!=''?$request->input('selected'):'';

        $data = T04tb::select('term')->where('class', $class)->groupBy('term')->get();

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
     * @param $course
     * @return string
     */
    function getCourse(Request $request)
    {
        $class = (string)$request->input('course');

        $term = $request->input('term');

        $selected = $request->input('selected')!=''?$request->input('selected'):'';

        $data = T06tb::select('course', 'name')->where('class', $class)->where('term', $term)->get();

        $result = '';

        // echo '<pre style="text-align:left;">' . "\n";
        // print_r($request->input('selected'));
        // echo "\n</pre>\n";
        // die();

        foreach ($data as $va) {
            $result .= '<option value="'.$va->course.'"';
            $result .= ($selected == $va->course)? ' selected>' : '>';
            $result .= $va->name.'</option>';
        }

        return $result;
    }

    function getlecthr(Request $request)
    {
        $class = (string)$request->input('class');

        $term = $request->input('term');

        $course = $request->input('course');

        $T06tb_data = T06tb::select('hour')->where('class', $class)->where('term', $term)->where('course', $course)->get()->toArray();
        $T06tb_data = $T06tb_data[0];

        $result = '0';

        if(!empty($T06tb_data['hour'])){
            $result = $T06tb_data['hour'];
        }


        return $result;
    }

    function getkind(Request $request)
    {

        $idno = $request->input('idno');

        $knid = M01tb::select('kind')->where('idno', $idno)->get()->toArray();
        $knid = $knid[0];

        $result = '0';

        if(!empty($knid['kind'])){
            $result = $knid['kind'];
        }


        return $result;
    }

    /**
     * 取得姓名
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    function getIdno(Request $request)
    {
        $keyword = $request->get('search');

        $query = M01tb::select('idno', 'cname', 'ename', 'education', 'dept');

        $query->where(function ($query) use ($keyword) {
            $query->where('cname', 'like', '%'.$keyword.'%')
                ->orwhere('ename', 'like', '%'.$keyword.'%');
        });

        $data = $query->get(10);

        $result = array();

        foreach ($data as $va) {

            $newData = array();
            $newData['id'] = $va->idno;
            $newData['text'] = $va->cname . $va->ename;
            $newData['text'] .= ($va->education)? '('.$va->education.')' : '';
            $newData['text'] .= ' '. $va->dept;

            $result[] = $newData;
        }

       return response()->json($result);
    }
}
