<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ArrangementService;
use App\Services\Term_processService;
use App\Services\User_groupService;
// use App\Models\T08tb;

use App\Models\S01tb;
use App\Models\M09tb;

use DateTime;
use DB ;
use Auth;

class ArrangementController extends Controller
{
    /**
     * WaitingController constructor.
     * @param ArrangementService $arrangementService
     */
    public function __construct(ArrangementService $arrangementService, Term_processService $term_processService, User_groupService $user_groupService)
    {
        setProgid('arrangement');
        $this->arrangementService = $arrangementService;
        $this->term_processService = $term_processService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('arrangement', $user_group_auth)){
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
    public function classList(Request $request)
    {
        $queryData = $request->only([
            't01tb.yerly',                // 年度
            't01tb.class',                // 班號
            't01tb.name',                 // 班級名稱
            't01tb.branchname',           // 分班名稱
            't01tb.branch',               // 辦班院區
            't01tb.process',              // 班別類型
            't01tb.commission',           // 委訓單位
            't01tb.traintype',            // 訓練性質
            't01tb.type',                 // 班別性質
            't01tb.categoryone',           // 類別1
            "t04tb.term",                  // 期別
            "t04tb.site_branch",           // 上課地點
            "t04tb.sponsor",                // 班務人員
            "t04tb.month",                 // 月份
            'sdate_start',                      // 開訓日期範圍(起)
            'sdate_end',                        // 開訓日期範圍(訖)
            'edate_start',                      // 結訓日期範圍(起)
            'edate_end',                        // 結訓日期範圍(訖)
            'training_start',                   // 在訓期間範圍(起)
            'training_end',                     // 在訓期間範圍(起)
            '_paginate_qty'
        ]);

        if (empty($queryData['t01tb']['yerly'])){
            $queryData['t01tb']['yerly'] = new DateTime();
            $queryData['t01tb']['yerly'] = $queryData['t01tb']['yerly']->format('Y') - 1911;
        }
        
        $s01tbM = S01tb::where('type', '=', 'M')->get()->pluck('name', 'code');
        $sponsors = M09tb::all();

        $data = [];
        if ($request->all()){
            $data = $this->arrangementService->getOpenClassList($queryData); // 取得開班資料
        }else{
            $sess = $request->session()->get('lock_class');
            if($sess){
                $queryData['t01tb']['class'] = $sess['class'];
                $queryData['t04tb']['term'] = $sess['term'];
                $queryData['t01tb']['yerly'] = substr($sess['class'], 0, 3);
                $data = $this->arrangementService->getOpenClassList($queryData);
            }
        }

        return view('admin/arrangement/list', compact('data', 'queryData', 'sponsors', 's01tbM'));
    }
    // public function index(Request $request)
    // {
    //     // 班號
	// 	if(null == $request->get('classno')) {
    //         $queryData['classno'] = 'null';
    //     }
	//     else {
    //         $queryData['classno'] = $request->get('classno')=='全部'?'':$request->get('classno');
    //     }
    //     // 期別
    //     $queryData['term'] = $request->get('term');
    //     // 每頁幾筆
    //     $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;
    //     $uesr = Auth::guard('managers')->user()->userid;

    //     $sql = "SELECT  X.class,  RTRIM(X.name) AS name
    //             FROM (SELECT A.class, A.name, 0 AS sort
    //                  FROM t01tb A
    //                  INNER JOIN t04tb B  ON A.class=B.class
    //                  INNER JOIN m09tb C  ON B.sponsor=C.userid
    //                  WHERE  A.type<>'13'  AND UPPER(B.sponsor)='".$uesr."'  GROUP BY A.class,A.name
    //                  UNION ALL
    //                  SELECT class,name,1 AS sort
    //                  FROM t01tb
    //             ) X  ORDER BY X.sort ASC,X.class DESC ";

    //     $data = DB::select($sql);

    //     $sql = "SELECT class,term,unit, RTRIM(name) name
    //             FROM t05tb
    //             WHERE class= '".$queryData['classno']."' AND term= '".$queryData['term']."'
    //             ORDER BY unit";

    //     $units = DB::select($sql);

    //     $sql = "SELECT t06tb.class, t06tb.term, course, t06tb.name t06tb_name, t05tb.name t05tb_name, hour
    //             FROM t06tb
    //             LEFT JOIN t05tb ON t05tb.class = t06tb.class AND t05tb.term = t06tb.term AND t05tb.unit = t06tb.unit
    //             WHERE t06tb.class= '".$queryData['classno']."' AND t06tb.term= '".$queryData['term']."'";

    //     $data2 = DB::select($sql);
    //     // dd($data2);
    //     return view('admin/arrangement/list', compact('data', 'queryData','data2', 'units'));
    // }

    /**
     * 進入維護頁面
     *
     */
    public function index($class, $term)
    {
        $class_info = [
            "class" => $class,
            "term" => $term
        ];
        $t04tb = $this->arrangementService->getT04tb($class_info);
        $hours_info = $this->arrangementService->getHoursInfo($class_info);
        $hours_info = $hours_info->pluck('total_hour', 'is_must_read')->toArray();
        return view('admin/arrangement/index', compact(['t04tb', 'hours_info']));
    }

    public function create($class, $term)
    {
        $class_info = [
            "class" => $class,
            "term" => $term
        ];
        $t04tb = $this->arrangementService->getT04tb($class_info);
        $classCategory = $this->getClassCategory();
        return view('admin/arrangement/form', compact('t04tb', 'classCategory'));
    }

    public function store(Request $request, $class, $term)
    {
        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('arrangement_class', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法新增');
        }

        $this->validate($request, [
            'name' => 'required'
        ],[
            "name.required" => "課程 欄位不可為空",
        ]);

        $t06tb = $request->only([
            "unit",
            "name",
            "hour",
            "matter",
            "category",
            "is_must_read"
        ]);

        $t06tb["class"] = $class;
        $t06tb["term"] = $term;

        $insert = $this->arrangementService->storeT06tb($t06tb, "insert");
        if ($insert){
            return back()->with('result', 1)->with('message', '新增成功');
        }else{
            return back()->with('result', 0)->with('message', '新增失敗');
        }
    }

    public function edit($class, $term, $course)
    {
        $course_info = [
            'class' => $class,
            'term' => $term,
            'course' => $course
        ];

        $t06tb = $this->arrangementService->getT06tb($course_info);
        $t04tb = $t06tb->t04tb;
        $classCategory = $this->getClassCategory();
        return view('admin/arrangement/form', compact('t04tb', 't06tb', 'classCategory'));
    }

    public function update(Request $request, $class, $term, $course)
    {
        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('arrangement_class', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法修改');
        }

        // $t06tb
        $t06tb = $request->only([
            "unit",
            "name",
            "hour",
            "matter",
            "category",
            "is_must_read"
        ]);
        $course_info = compact(['class', 'term', 'course']);

        if ($this->arrangementService->checkIsPayed($course_info)){
            return back()->with('result', 0)->with('message', '該課程已轉帳不可修改');
        }

        $update = $this->arrangementService->storeT06tb($t06tb, "update", $course_info);
        if ($update){
            return back()->with('result', 1)->with('message', '更新成功');
        }else{
            return back()->with('result', 0)->with('message', '更新失敗');
        }

    }

    // 共用查詢 取得『班別類別』 資料
    function getClassCategory() {
        $data = DB::select("SELECT serno, indent, CONCAT(name, ' ', category) as name, category, sequence FROM s03tb order by sequence+0");

        return $data;
    }

    function isHavePlanmk($class)
    {
        $t01tb = $this->arrangementService->getT01tb(compact(['class']));

        return response()->json(['status' => 1, 'have_planmk' => !empty($t01tb->planmk)]);
    }

    function uploadSchedule(Request $request, $class)
    {
        $class = $request->get('class');
        $term = $request->get('term');
        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('arrangement_upload', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法上傳');
        }

        $content = "";
        if (isset($_FILES['schedule'])){
            if (!empty($_FILES['schedule']['tmp_name'])){
                $content = file_get_contents($_FILES['schedule']['tmp_name']);
                $update = $this->arrangementService->uploadSchedule(compact(['class']), $content);

                if ($update){
                    return back()->with('result', 1)->with('message', '上傳成功');
                }
            }
        }
        return back()->with('result', 0)->with('message', '上傳失敗');

        // header("Cache-Control: no-cache private");
        // header("Content-Description: File Transfer");
        // header('Content-disposition: attachment; filename=test.pdf');
        // header("Content-Type: application/vnd.ms-excel");
        // header("Content-Transfer-Encoding: binary");
        // header('Content-Length: '. strlen($content));
        // echo $content;
        // exit;

        // dd($content);
    }

    public function batch_create()
    {

        return view('admin/arrangement/batch_create');
    }

    public function batch_store(Request $request)
    {

        $copy_info = $request->only(['copyed.class', 'copyed.term', 'copy_purpose.class', 'copy_purpose.term']);

        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('arrangement_class', $copy_info['copy_purpose']['class'], $copy_info['copy_purpose']['term']);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法新增');
        }

        $copy_purpose_t04tb = $this->arrangementService->getT04tb($copy_info['copy_purpose']);
        $copyed_t04tb = $this->arrangementService->getT04tb($copy_info['copyed']);

        if (empty($copy_purpose_t04tb)){
            return back()->with('result', '0')->with('message', '目的班別不存在')->withInput();
        }
        if (empty($copyed_t04tb)){
            return back()->with('result', '0')->with('message', '原始班別不存在')->withInput();
        }

        $copySchedule = isset($request->include_class_schedule);
        $copy = $this->arrangementService->copyT06tb($copy_info, $copySchedule); 

        if ($copy['status'] === 1){
            $message = "目的班期 {$copy_purpose_t04tb->t01tb->name} 第 {$copy_purpose_t04tb->class} 期";
            switch ($copy['exsit']) {
                case 't06tb':
                    $message .= "課程配當已有資料";
                    break;
                case 't05tb':
                    $message .= "課程單元配當已有資料";
                    break;
                case 't08tb':
                    $message .= "擬聘講座已有資料";
                    break;
                default:
                    # code...
                    break;
            }

            return back()->with('result', '0')->with('message', $message)->withInput();
        }elseif ($copy['status'] === true){
            return back()->with('result', '1')->with('message', '複製成功');            
        }elseif ($copy['status'] === false){
            return back()->with('result', '0')->with('message', '複製失敗');  
        }

    }

    public function delete($class, $term, $course)
    {
        //班務流程凍結
        $freeze = $this->term_processService->getFreeze('arrangement_class', $class, $term);
        if($freeze == 'Y'){
            return back()->with('result', 0)->with('message', '凍結中無法刪除');
        }

        // 檢查是否已轉帳，若已轉帳，則不可更新、及刪除課程資料
        $course_info = compact(['class', 'term', 'course']);

        if ($this->arrangementService->checkIsPayed($course_info)){
            return back()->with('result', 0)->with('message', '該課程已轉帳不可刪除');
        }
        
        $delete = $this->arrangementService->deleteT06tb($course_info);
        if ($delete){
            return redirect("/admin/arrangement/{$class}/{$term}")->with('result', 1)->with('message', '刪除成功');
        }else{
            return back()->with('result', 0)->with('message', '刪除失敗');
        }
    }
}