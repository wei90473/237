<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\DemandDistributionService;
use App\Services\User_groupService;
use App\Models\T69tb;
use App\Models\T01tb;
use App\Models\T02tb;
use App\Models\M17tb;
use App\Models\M13tb;
use App\Models\M07tb;


use DB;

/*
'【t01tb 班別基本資料】
'【t02tb 參訓單位報名資料】
'【t03tb 各期參訓單位報名檔
'【m13tb(機關基本資料)】
'【m07tb 訓練機構資料檔】
*/

class DemandDistributionController extends Controller
{
    /**
     * DemandDistributionController constructor.
     * @param DemandDistributionService $demandDistributionService
     */
    public function __construct(DemandDistributionService $demandDistributionService, User_groupService $user_groupService)
    {
        $this->demandDistributionService = $demandDistributionService;
        $this->user_groupService = $user_groupService;
        //檢查權限
        $this->middleware(function($request, $next){
            $user_data = \Auth::user();
            $user_group_auth = $this->user_groupService->getUser_auth($user_data->user_group_id);
            if(in_array('demand_distribution', $user_group_auth)){
                return $next($request);
            }else{
                return redirect('admin/home')->with('result', '0')->with('message', $this->user_group_msg);
            }
            // dd($user_data);
            // dd(\session());
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
        // 年度
        $queryData['yerly'] = $request->get('yerly');
        // 辦班院區
        $queryData['branch'] = $request->get('branch');
        // 第幾次調查
        $queryData['times'] = $request->get('times');
        // 班別代號
        $queryData['class'] = $request->get('class');
        // 班別名稱
        $queryData['classes_name'] = $request->get('classes_name');
        // 機關名稱
        $queryData['organ'] = $request->get('organ');
        // 排序欄位
        $queryData['_sort_field'] = $request->get('_sort_field');
        // 排序方向
        $queryData['_sort_mode'] = $request->get('_sort_mode');
        // 每頁幾筆
        $queryData['_paginate_qty'] = $request->get('_paginate_qty')? $request->get('_paginate_qty') : 20;

        if(empty($request->all())) {
            $queryData['choices'] = $this->_get_year_list();
            // return view('admin/demand_distribution/list', compact('queryData'));
        }
        // 取得列表資料
        $data = $this->demandDistributionService->getClassList($queryData);

        return view('admin/demand_distribution/list', compact('data', 'queryData'));
    }

    /**
     * 顯示頁
     *
     * @param $class
     */
    public function show($class)
    {
        return $this->edit($class);
    }

    /**
     * 編輯頁
     *
     * @param $class
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($class)
    {
        $query = T01tb::select('class', 'name', 'yerly', 'times', 'quotatot');

        $query->where('class', $class);

        $data = $query->first();

        if ( ! $data) {

            return view('admin/errors/error');
        }



        //$list had combined in $list2
        // $list2 = T69tb::select('m17tb.enrollorg AS enrollorg', 'm17tb.enrollname AS enrollname','t69tb.class AS class','t69tb.organ AS organ','t69tb.applycnt AS applycnt', 't69tb.checkcnt AS checkcnt')->leftJoin('m17tb', 't69tb.organ', '=', 'm17tb.enrollorg')->where('t69tb.class', $class)->get();

        // SELECT B.organ AS [機關代碼],CASE WHEN ISNULL(C.lname,'')<>'' THEN C.lname  ELSE ISNULL(D.name,'') END AS [機關名稱],
        // B.demand AS [需求人數],B.quota AS [已分配人數],B.organ,B.demand,B.quota,A.class
        // FROM t01tb A
        // INNER JOIN t02tb B ON A.class=B.class
        // LEFT JOIN m13tb C ON B.organ=C.organ
        // LEFT JOIN m07tb D ON B.organ=D.agency
        // WHERE A.class=" & AddQuot(strClass) ORDER BY C.rank


        // SELECT B.organ AS [機關代碼],CASE WHEN ISNULL(C.lname,'')<>'' THEN C.lname  ELSE ISNULL(D.name,'') END AS [機關名稱],
        // B.demand AS [需求人數],B.quota AS [已分配人數],B.organ,B.demand,B.quota,A.class
        // FROM t01tb A
        // INNER JOIN t02tb B ON A.class=B.class
        // LEFT JOIN m13tb C ON B.organ=C.organ
        // LEFT JOIN m07tb D ON B.organ=D.agency
        // WHERE A.class=" & AddQuot(strClass) ORDER BY C.rank

        $list2 = DB::select("SELECT B.organ AS organ ,CASE WHEN IFNULL(C.lname,'')<>'' THEN C.lname ELSE IFNULL(D.name,'') END AS names,
        B.demand AS people,B.quota AS have,B.organ,B.demand,B.quota,A.class, A.quotatot
        FROM t01tb A
        INNER JOIN t02tb B ON A.class=B.class
        LEFT JOIN m13tb C ON B.organ=C.organ
        LEFT JOIN m07tb D ON B.organ=D.agency
        WHERE A.class= $class
        ORDER BY C.rank");

        // $list2 = T01tb::select('t02tb.organ AS organ', 't02tb.demand AS demand', 't02tb.quota AS quota', 'm13tb.lname AS name')
        // ->join('t02tb', 't01tb.class', '=', 't02tb.class')
        // ->leftJoin('m13tb', 't02tb.organ', '=', 'm13tb.organ')
        // ->leftJoin('m07tb', 't02tb.organ', '=', 'm07tb.agency')
        // ->where('t01tb.class', $class)
        // ->orderBy('m13tb.rank', 'ASC')
        // ->get();


        // $list2 = T02tb::select('t02tb.class AS class02', 't02tb.organ AS organ', 't02tb.demand AS demand', 't02tb.quota AS qutoa', 't01tb.class AS class01', 't01tb.name AS name')->leftJoin('t01tb','t02tb.class','=','t01tb.class')->where('t02tb.class', $class)->get();

        //$list = T69tb::where('class', $class)->get();

        return view('admin/demand_distribution/form', compact('data', 'list2'));
    }

    public function edit2($class)
    {
        $query = T01tb::select('class', 'name', 'yerly', 'times', 'quotatot');

        $query->where('class', $class);

        $data = $query->first();

        if ( ! $data) {

            return view('admin/errors/error');
        }

        //$list = T69tb::where('class', $class)->get();

        //RTRIM 移除尾端空格
        //A(m13tb) organ: 機關代碼, lname: 機關名稱(全名)
        //B(t02tb) quota: 分配人數, demand: 需求人數

        $list2 = DB::select("SELECT A.organ AS organ ,RTRIM(A.lname) AS name,
         IFNULL(B.quota,0) AS have, A.organ, IFNULL(B.demand, 0) AS demand, IFNULL(B.quota, 0) AS quota2
        FROM m13tb A
        LEFT JOIN t02tb B
        ON A.organ = B.organ AND B.class = $class
       ");


         //$list had combined in $list2
         $list2 = T01tb::select('t02tb.organ AS organ', 't02tb.demand AS demand', 't02tb.quota AS quota', 'm13tb.lname AS name')
        ->join('t02tb', 't01tb.class', '=', 't02tb.class')
        ->leftJoin('m13tb', 't02tb.organ', '=', 'm13tb.organ')
        ->leftJoin('m07tb', 't02tb.organ', '=', 'm07tb.agency')
        ->where('t01tb.class', $class)
        ->orderBy('m13tb.rank', 'ASC')
        ->get();

        return view('admin/demand_distribution/form2', compact('data', 'list2'));
    }




     /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update_tune_quotatot(Request $request, $class)
    {

       $demandtatot = T02tb::where('class', $class)->sum('demand');
       $proportion  = $request->input('quotatot')/$demandtatot;
        // 更新總分配人數
       T01tb::where('class', $class)->update(array('quotatot' => $request->input('quotatot')));

       // 將該班等比例重新分配
       T02tb::where('class', $class)->update(array('quota' => (DB::raw('demand*'.$proportion.'')) ));


       return json_encode(array('success' => '已按照比例重新更新總分配人數!'));
    }


    /**
     *
     * 更新機關需求人數
     *
     */
    public function demand_orga_list_update(Request $request)
    {
        $data['orga']           = $request->input('orga'); //班號
        $data['class_quotatot'] = (is_array($request->input('class_quotatot')))? $request->input('class_quotatot') : array();

        $result = array();
        // 儲存資料庫格式轉換
         foreach ($data['class_quotatot'] as $class => $demand) {
            $result[] = array(
                'organ' =>  $data['orga'],
                'demand' => $demand,
                'class' => $class,
            );
        }
        // 儲存資料庫
        foreach ($result as $va) {

            T02tb::where('class', $va['class'])->where('organ', $va['organ'])->update($va);
        }


        return back()->with('result', '1')->with('message', '儲存成功!');
    }


    /**
     * 編輯頁更新處理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $class)
    {
        // 取得資料
        // $data['organ'] = (is_array($request->input('organ')))? $request->input('organ') : array();
        // $data['quota'] = (is_array($request->input('quota')))? $request->input('quota') : array();
        // $data['demand'] = (is_array($request->input('demand')))? $request->input('demand') : array();
        // $data['applycnt'] = (is_array($request->input('applycnt')))? $request->input('applycnt') : array();
        $data['checkcnt'] = (is_array($request->input('checkcnt')))? $request->input('checkcnt') : array();

        $result = array();
        $idAry = array();


        // 格式轉換
        // foreach ($data['organ'] as $key => $organ) {
        //     $result[] = array(
        //         'organ' => $data['organ'][$key],
        //         // 'applycnt' => $data['applycnt'][$key],
        //         'checkcnt' => $data['checkcnt'][$key],
        //         'class' => $class,
        //     );
        //     $idAry[] = $data['organ'][$key];
        // }

         // 格式轉換
        foreach ($data['checkcnt'] as $key => $organ) {
            $result[] = array(
                'organ' => $key,
                // 'applycnt' => $data['applycnt'][$key],
                'quota' => $organ,
                'class' => $class,
            );

        }


        // 儲存資料庫
        foreach ($result as $va) {
            // 確認是否存在
            if ( ! T02tb::where('class', $class)->where('organ', $va['organ'])->first()) {
                // 不存在時新增
                T02tb::create($va);

            } else {
                // 存在時更新
                T02tb::where('class', $class)->where('organ', $va['organ'])->update($va);
            }
        }

        // // 刪除不在陣列中的id
        // $data = T69tb::where('class', $class)->whereNotIn('organ', $idAry)->get();

        // foreach($data as $va){
        //     // 迴圈刪除
        //     $va->delete();
        // }

        // 更新班表需求人數
       // T01tb::where('class', $class)->update(array('quotatot' => $request->input('quotatot')));

        return back()->with('result', '1')->with('message', '儲存成功!');
    }


    public function GetTerm(Request $request)
    {
        // 取得年度year
        $queryData['yerly'] = $request->get('year');
        // 取得院區branch
        $queryData['branch'] = $request->get('branch');
        $data = $this->demandDistributionService->getTimesByYearBranchList($queryData);


        return $data;
    }

    public function _get_year_list()
    {
        $year_list = array();
        $year_now  = date('Y');
        $this_yesr = $year_now - 1910;

        for($i=$this_yesr; $i>=90; $i--){
            $year_list[$i] = $i;
        }
        // jd($year_list,1);
        return $year_list;
    }

    public function getOrgannameAndClassname(Request $request){

        $organ = DB::select('SELECT organ,lname FROM m13tb');
        $class = DB::select('SELECT name FROM t01tb');
        $data = $organ.$class;
        return $data;
    }

    public function demand_orga(Request $request){
        $array = array();
        // 年度
        $queryData['yerly'] = $request->get('yerly');
        if($queryData['yerly']!=''){
            $array['t01tb.yerly'] = $queryData['yerly'];
        }
        // // 辦班院區
        $queryData['branch'] = $request->get('branch');
        // if($queryData['branch']!='' ||  $queryData['branch']!='全部'){
        //     $array['t01tb.branch'] = $queryData['branch'];
        // }
        // 第幾次調查
        $queryData['times'] = $request->get('times');
        if($queryData['times']!=''){
            $array['t01tb.times'] = $queryData['times'];
        }
        // 機關代碼
        $queryData['organ'] = $request->get('organname');
        if($queryData['organ']!=''){
            $array['t02tb.organ'] = $queryData['organ'];
        }
        // // // 機關名稱
        // $queryData['organname'] = $request->get('organname');
        // if($queryData['organname'] !='全部'){
        //     $array['m13tb.lname'] = $queryData['organname'];
        // }
         //$list had combined in $list2
         $list2 = T01tb::select('t01tb.class AS classno','t01tb.name AS classname','t02tb.organ AS organ', 't02tb.demand AS demand', 't02tb.quota AS quota', 'm13tb.lname AS name')
        ->join('t02tb', 't01tb.class', '=', 't02tb.class')
        ->leftJoin('m13tb', 't02tb.organ', '=', 'm13tb.organ')
        ->leftJoin('m07tb', 't02tb.organ', '=', 'm07tb.agency')
        ->where($array )
        ->orderBy('t01tb.class', 'ASC')
        ->get();


        return json_encode($list2);
    }

    public function demand_classes(Request $request){



        // 班期代碼
        $queryData['class'] = $request->get('class');
        // 班期名稱
        $queryData['classesname'] = $request->get('classesname');

        $list2 = DB::select("SELECT A.quotatot AS quotatot,A.class AS classno,A.name AS classname,sum( B.demand) AS demand, sum(B.quota) AS quota
        FROM t01tb A
        LEFT JOIN t02tb B ON A.class=B.class
        LEFT JOIN m13tb C ON B.organ=C.organ
        LEFT JOIN m07tb D ON B.organ=D.agency
        WHERE A.class= '{$queryData['class']}' and A.name like '%{$queryData['classesname']}%'
        Group By A.class,A.name
        ORDER BY A.class asc");


        //  $list2 = T01tb::select('t01tb.quotatot AS quotatot','t01tb.class AS classno','t01tb.name AS classname','t02tb.demand AS demand', 't02tb.quota AS quota')
        // ->join('t02tb'    , 't01tb.class', '=', 't02tb.class')
        // ->leftJoin('m13tb', 't02tb.organ', '=', 'm13tb.organ')
        // ->leftJoin('m07tb', 't02tb.organ', '=', 'm07tb.agency')
        // ->where($array)
        // ->where('t01tb.name','like' , "%{$queryData['classesname']}%")
        // ->groupBy(['t01tb.class','t01tb.name'])
        // ->orderBy('t01tb.class', 'ASC')
        // ->get();

        return json_encode($list2);
    }

    function escape_like_str($str)
    {
        $like_escape_char = '!';

        return str_replace([$like_escape_char, '%', '_'], [
            $like_escape_char.$like_escape_char,
            $like_escape_char.'%',
            $like_escape_char.'_',
        ], $str);
    }

}
